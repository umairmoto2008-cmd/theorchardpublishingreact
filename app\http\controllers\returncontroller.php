<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\ProductReturnItem;
use App\Models\InventoryLog;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $returns = ProductReturn::with(['sale', 'items', 'user'])->latest()->paginate(15);
        $totalRefundsAllTime = ProductReturn::sum('total_refund');
        $totalReturnsCount = ProductReturn::count();

        // Recent sales with items for instant 1-click modal returns
        $recentSales = Sale::with(['items.product', 'user'])->latest()->take(20)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('returns.index', compact('returns', 'totalRefundsAllTime', 'totalReturnsCount', 'recentSales', 'products'));
    }

    public function create(Request $request)
    {
        $invoiceNumber = trim($request->get('invoice_number', ''));
        $sale = null;

        if ($invoiceNumber) {
            // Flexible search: match exact, or partial like '6' -> 'HG-INV-6'
            $sale = Sale::with(['items.product', 'user'])
                ->where('invoice_number', $invoiceNumber)
                ->orWhere('invoice_number', 'like', "%{$invoiceNumber}%")
                ->orWhere('customer_phone', 'like', "%{$invoiceNumber}%")
                ->orWhere('customer_name', 'like', "%{$invoiceNumber}%")
                ->first();
        }

        // Recent 8 sales for quick 1-click selection
        $recentSales = Sale::with(['items'])->latest()->take(8)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('returns.create', compact('sale', 'invoiceNumber', 'recentSales', 'products'));
    }

    public function store(Request $request)
    {
        $returnMode = $request->input('return_mode', 'invoice'); // 'invoice' or 'direct'

        if ($returnMode === 'direct') {
            // Direct item return without invoice
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:0.01',
                'refund_amount' => 'required|numeric|min:0',
                'customer_name' => 'nullable|string|max:100',
                'reason' => 'required|string|max:500',
            ]);

            try {
                DB::beginTransaction();

                $product = Product::findOrFail($validated['product_id']);
                $qty = (float) $validated['quantity'];
                $refundAmt = (float) $validated['refund_amount'];

                $lastReturnId = ProductReturn::max('id') ?? 100;
                $returnNumber = 'HG-RET-' . ($lastReturnId + 1);

                $productReturn = ProductReturn::create([
                    'return_number' => $returnNumber,
                    'sale_id' => null,
                    'customer_name' => $validated['customer_name'] ?: 'Walk-in Customer',
                    'total_refund' => $refundAmt,
                    'reason' => $validated['reason'],
                    'user_id' => Auth::id(),
                ]);

                $productReturn->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->sale_price,
                    'refund_amount' => $refundAmt,
                ]);

                // Restore stock
                $oldStock = (float) $product->stock_quantity;
                $newStock = $oldStock + $qty;
                $product->update(['stock_quantity' => $newStock]);

                InventoryLog::create([
                    'product_id' => $product->id,
                    'type' => 'return_inward',
                    'quantity' => $qty,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'reference_type' => 'return',
                    'reference_id' => $returnNumber,
                    'notes' => 'Direct return: ' . $validated['reason'],
                    'user_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('returns.index')->with('success', "Direct Return {$returnNumber} processed! Restored {$qty} {$product->unit} to stock. Refund: Rs. " . number_format($refundAmt, 0));
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error processing direct return: ' . $e->getMessage());
            }
        }

        // Return with invoice
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'reason' => 'nullable|string|max:500',
            'returns' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::with('items')->findOrFail($validated['sale_id']);
            $totalRefund = 0;
            $itemsToReturn = [];

            foreach ($validated['returns'] as $retData) {
                if (!isset($retData['sale_item_id'])) continue;
                
                $qty = isset($retData['quantity']) ? (float) $retData['quantity'] : 0;
                if ($qty <= 0) continue; // Skip unreturned items cleanly

                $saleItem = $sale->items->firstWhere('id', $retData['sale_item_id']);
                if (!$saleItem) continue;

                if ($qty > $saleItem->quantity) {
                    $qty = (float) $saleItem->quantity;
                }

                $refundLine = $qty * $saleItem->unit_price;
                $totalRefund += $refundLine;

                $itemsToReturn[] = [
                    'sale_item' => $saleItem,
                    'quantity' => $qty,
                    'unit_price' => $saleItem->unit_price,
                    'refund_amount' => $refundLine,
                ];
            }

            if (empty($itemsToReturn)) {
                return back()->with('error', 'Please enter a return quantity greater than 0 for at least one item.');
            }

            $lastReturnId = ProductReturn::max('id') ?? 100;
            $returnNumber = 'HG-RET-' . ($lastReturnId + 1);

            $productReturn = ProductReturn::create([
                'return_number' => $returnNumber,
                'sale_id' => $sale->id,
                'customer_name' => $sale->customer_name,
                'total_refund' => $totalRefund,
                'reason' => $validated['reason'] ?? 'Customer Return / Exchange',
                'user_id' => Auth::id(),
            ]);

            foreach ($itemsToReturn as $item) {
                $saleItem = $item['sale_item'];
                $productReturn->items()->create([
                    'product_id' => $saleItem->product_id,
                    'product_name' => $saleItem->product_name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'refund_amount' => $item['refund_amount'],
                ]);

                // Restore stock
                if ($saleItem->product_id) {
                    $product = Product::find($saleItem->product_id);
                    if ($product) {
                        $oldStock = (float) $product->stock_quantity;
                        $newStock = $oldStock + $item['quantity'];
                        $product->update(['stock_quantity' => $newStock]);

                        InventoryLog::create([
                            'product_id' => $product->id,
                            'type' => 'return_inward',
                            'quantity' => $item['quantity'],
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock,
                            'reference_type' => 'return',
                            'reference_id' => $returnNumber,
                            'notes' => 'Customer return from invoice ' . $sale->invoice_number,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }
            }

            // Check if full sale is returned or partial
            $totalSaleQty = $sale->items->sum('quantity');
            $totalReturnedQty = \App\Models\ProductReturnItem::whereHas('productReturn', function ($q) use ($sale) {
                $q->where('sale_id', $sale->id);
            })->sum('quantity');

            $newStatus = $totalReturnedQty >= $totalSaleQty ? 'returned' : 'partial_return';
            $sale->update(['status' => $newStatus]);

            DB::commit();

            return redirect()->route('returns.index')->with('success', "Return {$returnNumber} processed successfully! Total Refund: Rs. " . number_format($totalRefund, 0) . " has been adjusted.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: ' . $e->getMessage());
        }
    }

    public function thermalReceipt(ProductReturn $productReturn)
    {
        $productReturn->load(['sale', 'items', 'user']);
        
        $storeSettings = [
            'name' => Setting::get('store_name', 'Hashmi Grains'),
            'tagline' => Setting::get('store_tagline', 'Pure by Nature'),
            'address' => Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore'),
            'phone' => Setting::get('store_phone', '0300-1234567'),
            'currency' => Setting::get('currency_symbol', 'Rs.'),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
        ];

        return view('returns.thermal', compact('productReturn', 'storeSettings'));
    }
}
