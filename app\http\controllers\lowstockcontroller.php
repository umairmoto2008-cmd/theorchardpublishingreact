<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class LowStockController extends Controller
{
    public function index()
    {
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->with('category')
            ->orderBy('stock_quantity')
            ->get();

        return view('inventory.low_stock', compact('lowStockProducts'));
    }
}
