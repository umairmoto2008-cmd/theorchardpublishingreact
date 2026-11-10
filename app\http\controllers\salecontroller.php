<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Setting;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $paymentMethod = $request->get('payment_method');

        $query = Sale::with(['items', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', Carbon::parse($dateTo));
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $totalSalesAmount = (clone $query)->sum('grand_total');
        $totalProfitAmount = (clone $query)->sum('profit');

        $sales = $query->latest()->paginate(15)->withQueryString();

        return view('sales.index', compact('sales', 'search', 'dateFrom', 'dateTo', 'paymentMethod', 'totalSalesAmount', 'totalProfitAmount'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'returns.items']);
        
        $storeSettings = [
            'name' => Setting::get('store_name', 'Hashmi Grains'),
            'tagline' => Setting::get('store_tagline', 'Pure by Nature'),
            'address' => Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore'),
            'phone' => Setting::get('store_phone', '0300-1234567'),
            'currency' => Setting::get('currency_symbol', 'Rs.'),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
        ];

        return view('sales.show', compact('sale', 'storeSettings'));
    }

    public function thermalReceipt(Sale $sale)
    {
        $sale->load(['items.product', 'user']);
        
        $storeSettings = [
            'name' => Setting::get('store_name', 'Hashmi Grains'),
            'tagline' => Setting::get('store_tagline', 'Pure by Nature'),
            'address' => Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore'),
            'phone' => Setting::get('store_phone', '0300-1234567'),
            'currency' => Setting::get('currency_symbol', 'Rs.'),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
        ];

        return view('sales.thermal', compact('sale', 'storeSettings'));
    }
}
