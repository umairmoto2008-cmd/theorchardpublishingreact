<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'Hashmi Grains'),
            'store_tagline' => Setting::get('store_tagline', 'Pure by Nature'),
            'store_address' => Setting::get('store_address', 'Shop #47, Overseas B Commercial, Bahria Town Lahore'),
            'store_phone' => Setting::get('store_phone', '0300-1234567'),
            'store_whatsapp' => Setting::get('store_whatsapp', '923001234567'),
            'currency_symbol' => Setting::get('currency_symbol', 'Rs.'),
            'receipt_header' => Setting::get('receipt_header', ''),
            'receipt_footer' => Setting::get('receipt_footer', ''),
            'default_alert_quantity' => Setting::get('default_alert_quantity', '5'),
            'tax_percentage' => Setting::get('tax_percentage', '0'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:150',
            'store_tagline' => 'nullable|string|max:200',
            'store_address' => 'required|string|max:300',
            'store_phone' => 'nullable|string|max:50',
            'store_whatsapp' => 'nullable|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'receipt_header' => 'nullable|string|max:1000',
            'receipt_footer' => 'nullable|string|max:1000',
            'default_alert_quantity' => 'required|numeric|min:1',
            'tax_percentage' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $val) {
            Setting::set($key, $val);
        }

        return back()->with('success', 'Shop settings saved successfully!');
    }
}
