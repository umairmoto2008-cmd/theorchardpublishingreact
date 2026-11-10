<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        $productId = $request->get('product_id');
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        // Products for Quick Stock Edit table
        $prodQuery = Product::with(['category', 'inventoryLogs' => function($q) {
            $q->latest()->with('user');
        }])->where('is_active', true);

        if ($search) {
            $prodQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $prodQuery->where('category_id', $categoryId);
        }

        $products = $prodQuery->orderBy('name')->paginate(25)->withQueryString();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        // Logs Query
        $logQuery = InventoryLog::with(['product', 'user']);
        if ($type) {
            $logQuery->where('type', $type);
        }
        if ($productId) {
            $logQuery->where('product_id', $productId);
        }
        $logs = $logQuery->latest()->paginate(25)->withQueryString();

        return view('inventory.logs', compact('products', 'categories', 'logs', 'type', 'productId', 'search', 'categoryId'));
    }

    public function quickStockUpdate(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldStock = (float) $product->stock_quantity;
        $newStock = (float) $validated['stock_quantity'];
        $diff = $newStock - $oldStock;

        $product->stock_quantity = $newStock;
        if (isset($validated['alert_quantity'])) {
            $product->alert_quantity = (float) $validated['alert_quantity'];
        }
        $product->save();

        if (abs($diff) > 0) {
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => $diff >= 0 ? 'manual_edit' : 'damage_adjustment',
                'quantity' => abs($diff),
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'reference_type' => 'quick_edit',
                'notes' => 'Direct shelf count update from Inventory Table',
                'user_id' => Auth::id(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stock for '{$product->name}' saved to {$newStock} successfully!",
                'product_id' => $product->id,
                'stock_quantity' => $newStock,
                'alert_quantity' => (float)$product->alert_quantity,
                'is_low' => $product->isLowStock(),
            ]);
        }

        return back()->with('success', "Stock for '{$product->name}' saved to {$newStock} successfully!");
    }

    public function quickAdjustDelta(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'delta' => 'required|numeric',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldStock = (float) $product->stock_quantity;
        $delta = (float) $validated['delta'];
        $newStock = max(0, $oldStock + $delta);

        $product->stock_quantity = $newStock;
        $product->save();

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => $delta >= 0 ? 'purchase_inward' : 'damage_adjustment',
            'quantity' => abs($delta),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => 'quick_adjust',
            'notes' => ($delta >= 0 ? "+{$delta}" : "{$delta}") . ' quick stock adjustment',
            'user_id' => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stock for '{$product->name}' adjusted by " . ($delta >= 0 ? "+{$delta}" : "{$delta}") . " (New Total: {$newStock})",
                'product_id' => $product->id,
                'stock_quantity' => $newStock,
                'alert_quantity' => (float)$product->alert_quantity,
                'is_low' => $product->isLowStock(),
            ]);
        }

        return back()->with('success', "Stock for '{$product->name}' adjusted by " . ($delta >= 0 ? "+{$delta}" : "{$delta}") . " (New Total: {$newStock})");
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:purchase_inward,damage_adjustment,manual_edit,sale_outward,set_exact',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldStock = (float) $product->stock_quantity;
        $qty = (float) $validated['quantity'];

        if ($validated['type'] === 'set_exact') {
            $newStock = max(0, $qty);
            $diff = $newStock - $oldStock;
            $logQty = abs($diff);
            $logType = $diff >= 0 ? 'manual_edit' : 'damage_adjustment';
        } elseif (in_array($validated['type'], ['damage_adjustment', 'sale_outward'])) {
            $newStock = max(0, $oldStock - $qty);
            $logQty = $qty;
            $logType = $validated['type'];
        } else {
            $newStock = $oldStock + $qty;
            $logQty = $qty;
            $logType = $validated['type'];
        }

        $product->stock_quantity = $newStock;
        $product->save();

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => $logType,
            'quantity' => $logQty,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => 'adjustment',
            'notes' => $validated['notes'],
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', "Inventory for '{$product->name}' adjusted successfully! New Stock: {$newStock} {$product->unit}");
    }

    public function update(Request $request, InventoryLog $inventoryLog)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'new_stock' => 'nullable|numeric|min:0',
        ]);

        $product = $inventoryLog->product;

        if ($request->filled('new_stock') && $product) {
            $targetStock = (float) $validated['new_stock'];
            $oldStock = (float) $product->stock_quantity;
            $diff = $targetStock - $oldStock;

            $product->stock_quantity = max(0, $targetStock);
            $product->save();

            $inventoryLog->new_stock = $targetStock;
            if (abs($diff) > 0) {
                $inventoryLog->quantity = abs($diff);
                $inventoryLog->type = $diff >= 0 ? 'manual_edit' : 'damage_adjustment';
            }
        } elseif (isset($validated['quantity']) && (float)$validated['quantity'] != (float)$inventoryLog->quantity) {
            $oldQty = (float) $inventoryLog->quantity;
            $newQty = (float) $validated['quantity'];
            $diff = $newQty - $oldQty;

            if ($product) {
                if (in_array($inventoryLog->type, ['sale_outward', 'damage_adjustment'])) {
                    $product->stock_quantity = max(0, (float)$product->stock_quantity - $diff);
                } else {
                    $product->stock_quantity = max(0, (float)$product->stock_quantity + $diff);
                }
                $product->save();
            }

            $inventoryLog->quantity = $newQty;
            $inventoryLog->new_stock = (float)$inventoryLog->old_stock + (in_array($inventoryLog->type, ['sale_outward', 'damage_adjustment']) ? -$newQty : $newQty);
        }

        if (isset($validated['notes'])) {
            $inventoryLog->notes = $validated['notes'];
        }

        $inventoryLog->save();

        return back()->with('success', "Stock for '" . ($product->name ?? 'Product') . "' updated successfully!");
    }

    public function destroy(Request $request, InventoryLog $inventoryLog)
    {
        $restoreStock = $request->boolean('restore_stock', true);

        if ($restoreStock && $inventoryLog->product) {
            $product = $inventoryLog->product;
            $qty = (float) $inventoryLog->quantity;

            if (in_array($inventoryLog->type, ['sale_outward', 'damage_adjustment'])) {
                $product->stock_quantity = (float)$product->stock_quantity + $qty;
            } else {
                $product->stock_quantity = max(0, (float)$product->stock_quantity - $qty);
            }
            $product->save();
        }

        $inventoryLog->delete();

        return back()->with('success', 'Inventory log entry deleted and product stock adjusted!');
    }
}
