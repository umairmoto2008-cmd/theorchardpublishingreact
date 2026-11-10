<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryLog;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        $storeSettings = [
            'name' => Setting::get('store_name', 'Hashmi Grains'),
            'tagline' => Setting::get('store_tagline', 'Pure by Nature'),
            'address' => Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore'),
            'phone' => Setting::get('store_phone', '0300-1234567'),
            'whatsapp' => Setting::get('store_whatsapp', '923001234567'),
            'currency' => Setting::get('currency_symbol', 'Rs.'),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
        ];

        return view('pos.index', compact('categories', 'products', 'storeSettings'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = Product::where('is_active', true)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('barcode', 'like', "%{$query}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->with('category')
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'cash_given' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalCost = 0;
            $saleItemsData = [];

            // Fetch and lock products
            $productIds = collect($validated['items'])->pluck('id')->all();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['id']);
                if (!$product) {
                    throw new \Exception("Product ID {$item['id']} not found.");
                }

                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['price'];
                $lineSubtotal = $qty * $unitPrice;
                $lineCost = $qty * (float) $product->purchase_price;
                $lineProfit = $lineSubtotal - $lineCost;

                $subtotal += $lineSubtotal;
                $totalCost += $lineCost;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'unit_cost' => $product->purchase_price,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineSubtotal,
                    'profit' => $lineProfit,
                ];

                // Deduct stock
                $oldStock = (float) $product->stock_quantity;
                $newStock = max(0, $oldStock - $qty);
                $product->stock_quantity = $newStock;
                $product->save();
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $profit = $grandTotal - $totalCost; // Accurately records profit or loss
            $cashGiven = $validated['payment_method'] === 'cash' ? (float) ($validated['cash_given'] ?? $grandTotal) : $grandTotal;
            $changeAmount = $validated['payment_method'] === 'cash' ? max(0, $cashGiven - $grandTotal) : 0;

            // Generate unique invoice number: HG-INV-XXXX
            $lastId = Sale::max('id') ?? 1000;
            $invoiceNumber = 'HG-INV-' . ($lastId + 1);

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'grand_total' => $grandTotal,
                'total_cost' => $totalCost,
                'profit' => $profit,
                'payment_method' => $validated['payment_method'],
                'cash_given' => $cashGiven,
                'change_amount' => $changeAmount,
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id() ?? 1,
            ]);

            foreach ($saleItemsData as $itemData) {
                $saleItem = $sale->items()->create($itemData);

                // Audit log for stock outward
                $product = $products->get($itemData['product_id']);
                InventoryLog::create([
                    'product_id' => $itemData['product_id'],
                    'type' => 'sale_outward',
                    'quantity' => $itemData['quantity'],
                    'old_stock' => $product->stock_quantity + $itemData['quantity'],
                    'new_stock' => $product->stock_quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->invoice_number,
                    'notes' => 'Sold in invoice ' . $sale->invoice_number,
                    'user_id' => Auth::id() ?? 1,
                ]);
            }

            DB::commit();

            // WhatsApp Message Text construction
            $storeName = Setting::get('store_name', 'Hashmi Grains');
            $storeAddress = Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore');
            $storePhone = Setting::get('store_phone', '0300-1234567');
            
            $waLines = [];
            $waLines[] = "*" . strtoupper($storeName) . "*";
            $waLines[] = "🌿 _Pure by Nature_";
            $waLines[] = "📍 " . $storeAddress;
            $waLines[] = "📞 " . $storePhone;
            $waLines[] = "--------------------------------";
            $waLines[] = "🧾 *Invoice:* " . $sale->invoice_number;
            $waLines[] = "📅 *Date:* " . $sale->created_at->format('d-M-Y h:i A');
            $waLines[] = "👤 *Customer:* " . ($sale->customer_name ?: 'Valued Customer');
            $waLines[] = "--------------------------------";
            $waLines[] = "*ITEMS PURCHASED:*";
            foreach ($saleItemsData as $item) {
                $waLines[] = "• " . $item['product_name'] . " (" . $item['quantity'] . " " . $item['unit'] . ") = Rs." . number_format($item['subtotal'], 0);
            }
            $waLines[] = "--------------------------------";
            $waLines[] = "*Subtotal:* Rs." . number_format($sale->subtotal, 0);
            if ($sale->discount > 0) {
                $waLines[] = "*Discount:* -Rs." . number_format($sale->discount, 0);
            }
            $waLines[] = "💰 *TOTAL AMOUNT:* Rs." . number_format($sale->grand_total, 0);
            $waLines[] = "*Payment:* " . ucfirst($sale->payment_method);
            if ($sale->cash_given > 0 && $sale->payment_method === 'cash') {
                $waLines[] = "*Cash Paid:* Rs." . number_format($sale->cash_given, 0);
                $waLines[] = "*Change:* Rs." . number_format($sale->change_amount, 0);
            }
            $waLines[] = "--------------------------------";
            $waLines[] = "✨ _Thank you for choosing Hashmi Grains! Organic & Pure._";

            $waText = implode("\n", $waLines);
            $encodedWaText = urlencode($waText);

            $cleanPhone = preg_replace('/[^0-9]/', '', $sale->customer_phone ?? '');
            if ($cleanPhone) {
                if (str_starts_with($cleanPhone, '03')) {
                    $cleanPhone = '92' . substr($cleanPhone, 1);
                }
            }
            $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text={$encodedWaText}" : "https://api.whatsapp.com/send?text={$encodedWaText}";

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale' => $sale->load(['items', 'user']),
                'whatsapp_url' => $waUrl,
                'whatsapp_text' => $waText,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
