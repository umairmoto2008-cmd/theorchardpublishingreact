<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $stockFilter = $request->get('stock_filter');
        $sort = $request->get('sort', 'name_asc');

        $query = Product::with('category')->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockFilter === 'low') {
            $query->whereColumn('stock_quantity', '<=', 'alert_quantity');
        } elseif ($stockFilter === 'out') {
            $query->where('stock_quantity', '<=', 0);
        }

        // Sorting
        switch ($sort) {
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'price_low':
                $query->orderBy('sale_price');
                break;
            case 'price_high':
                $query->orderByDesc('sale_price');
                break;
            case 'stock_low':
                $query->orderBy('stock_quantity');
                break;
            case 'stock_high':
                $query->orderByDesc('stock_quantity');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name');
                break;
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // High level catalog stats
        $totalProductsCount = Product::where('is_active', true)->count();
        $lowStockCount = Product::where('is_active', true)->whereColumn('stock_quantity', '<=', 'alert_quantity')->count();
        $outOfStockCount = Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count();
        $totalValuationCost = Product::where('is_active', true)->selectRaw('SUM(stock_quantity * purchase_price) as val')->value('val') ?? 0;
        $totalValuationSale = Product::where('is_active', true)->selectRaw('SUM(stock_quantity * sale_price) as val')->value('val') ?? 0;

        return view('products.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'stockFilter',
            'sort',
            'totalProductsCount',
            'lowStockCount',
            'outOfStockCount',
            'totalValuationCost',
            'totalValuationSale'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['barcode'])) {
            $validated['barcode'] = 'HG-' . strtoupper(Str::random(8));
        }

        $validated['alert_quantity'] = $validated['alert_quantity'] ?? 5;
        $validated['is_active'] = true;

        $product = Product::create($validated);

        // Record opening inventory log
        if ($product->stock_quantity > 0) {
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => 'purchase_inward',
                'quantity' => $product->stock_quantity,
                'old_stock' => 0,
                'new_stock' => $product->stock_quantity,
                'reference_type' => 'manual',
                'notes' => 'Initial stock on creation',
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' added successfully!");
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'expiry_date' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        if (isset($validated['stock_quantity']) && (float)$validated['stock_quantity'] != (float)$product->stock_quantity) {
            $oldStock = (float) $product->stock_quantity;
            $newStock = (float) $validated['stock_quantity'];
            $diff = $newStock - $oldStock;

            InventoryLog::create([
                'product_id' => $product->id,
                'type' => $diff >= 0 ? 'manual_edit' : 'damage_adjustment',
                'quantity' => abs($diff),
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'reference_type' => 'manual_edit',
                'notes' => 'Stock updated via product edit',
                'user_id' => Auth::id(),
            ]);
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function updateDirect(Request $request)
    {
        $product = Product::findOrFail($request->input('product_id'));
        return $this->update($request, $product);
    }

    public function quickRestockSubmit(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $addQty = (float) $validated['quantity'];
        $oldStock = (float) $product->stock_quantity;
        $newStock = $oldStock + $addQty;

        if (!empty($validated['purchase_price']) && $validated['purchase_price'] > 0) {
            $product->purchase_price = $validated['purchase_price'];
        }

        $product->stock_quantity = $newStock;
        $product->save();

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'purchase_inward',
            'quantity' => $addQty,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => 'purchase',
            'notes' => $validated['notes'] ?? 'Restocked inventory',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('products.index')->with('success', "Restocked {$addQty} {$product->unit} for '{$product->name}' successfully! Total Stock: {$newStock} {$product->unit}");
    }

    public function quickRestock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $addQty = (float) $validated['quantity'];
        $oldStock = (float) $product->stock_quantity;
        $newStock = $oldStock + $addQty;

        if (!empty($validated['purchase_price']) && $validated['purchase_price'] > 0) {
            $product->purchase_price = $validated['purchase_price'];
        }

        $product->stock_quantity = $newStock;
        $product->save();

        InventoryLog::create([
            'product_id' => $product->id,
            'type' => 'purchase_inward',
            'quantity' => $addQty,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'reference_type' => 'purchase',
            'notes' => $validated['notes'] ?? 'Restocked inventory',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('products.index')->with('success', "Restocked {$addQty} {$product->unit} for '{$product->name}' successfully! Total Stock: {$newStock} {$product->unit}");
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();
        return redirect()->route('products.index')->with('success', "Product '{$productName}' removed successfully!");
    }

    public function bulkAddView()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.bulk_add', compact('categories'));
    }

    public function bulkStore(Request $request)
    {
        $rows = $request->input('products', []);
        $addedCount = 0;

        foreach ($rows as $row) {
            if (empty($row['name']) || empty($row['category_id']) || !isset($row['sale_price'])) {
                continue;
            }

            $barcode = !empty($row['barcode']) ? trim($row['barcode']) : 'HG-' . strtoupper(Str::random(8));
            $stock = isset($row['stock_quantity']) ? (float)$row['stock_quantity'] : 0;

            $product = Product::create([
                'name' => trim($row['name']),
                'barcode' => $barcode,
                'category_id' => $row['category_id'],
                'unit' => $row['unit'] ?? 'Kg',
                'purchase_price' => (float)($row['purchase_price'] ?? 0),
                'sale_price' => (float)($row['sale_price'] ?? 0),
                'stock_quantity' => $stock,
                'alert_quantity' => (float)($row['alert_quantity'] ?? 5),
                'is_active' => true,
            ]);

            if ($stock > 0) {
                InventoryLog::create([
                    'product_id' => $product->id,
                    'type' => 'purchase_inward',
                    'quantity' => $stock,
                    'old_stock' => 0,
                    'new_stock' => $stock,
                    'reference_type' => 'bulk_entry',
                    'notes' => 'Bulk product creation',
                    'user_id' => Auth::id(),
                ]);
            }

            $addedCount++;
        }

        return redirect()->route('products.index')->with('success', "Successfully added {$addedCount} products in bulk!");
    }

    public function exportCsv()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $filename = "hashmi_grains_products_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Product Name', 'Barcode', 'Category', 'Unit', 'Purchase Price (Rs)', 'Sale Price (Rs)', 'Current Stock', 'Alert Quantity', 'Margin %']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->name,
                    $p->barcode,
                    $p->category ? $p->category->name : 'Uncategorized',
                    $p->unit,
                    $p->purchase_price,
                    $p->sale_price,
                    $p->stock_quantity,
                    $p->alert_quantity,
                    $p->margin . '%',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $defaultCat = Category::firstOrCreate(['name' => 'General Grocery'], ['slug' => 'general-grocery', 'icon' => '📦']);

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0])) continue;

            $name = trim($row[0]);
            $barcode = !empty($row[1]) ? trim($row[1]) : 'HG-' . strtoupper(Str::random(8));
            $catName = !empty($row[2]) ? trim($row[2]) : 'General Grocery';
            $unit = !empty($row[3]) ? trim($row[3]) : 'Kg';
            $purchasePrice = isset($row[4]) ? (float)$row[4] : 0;
            $salePrice = isset($row[5]) ? (float)$row[5] : 0;
            $stock = isset($row[6]) ? (float)$row[6] : 0;
            $alert = isset($row[7]) ? (float)$row[7] : 5;

            $category = Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName), 'icon' => '🌾']
            );

            $product = Product::updateOrCreate(
                ['barcode' => $barcode],
                [
                    'name' => $name,
                    'category_id' => $category->id,
                    'unit' => $unit,
                    'purchase_price' => $purchasePrice,
                    'sale_price' => $salePrice,
                    'stock_quantity' => $stock,
                    'alert_quantity' => $alert,
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return redirect()->route('products.index')->with('success', "Imported {$imported} products successfully from file!");
    }

    public function barcodesSheet(Request $request)
    {
        $productId = $request->get('product_id') ?: $request->get('id');
        $barcode = $request->get('barcode') ?: $request->get('search');
        $copies = max(1, (int) $request->get('copies', 1));

        $query = Product::where('is_active', true);

        if ($productId) {
            $query->where('id', $productId);
        } elseif ($barcode) {
            $query->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)
                  ->orWhere('name', 'like', "%{$barcode}%");
            });
        }

        $products = $query->orderBy('name')->get();
        $isSingleProduct = ($productId || $barcode) && $products->count() === 1;
        $singleProduct = $isSingleProduct ? $products->first() : null;
        $customText = $request->get('custom') ?: '';
        $autoPrint = $request->get('auto') == '1';

        return view('products.barcodes', compact('products', 'isSingleProduct', 'singleProduct', 'copies', 'customText', 'autoPrint'));
    }
}
