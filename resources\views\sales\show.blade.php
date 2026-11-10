@extends('layouts.app')

@section('title', 'Invoice ' . $sale->invoice_number . ' - Hashmi Grains')
@section('header_title', 'Invoice Details: ' . $sale->invoice_number)

@section('styles')
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        .invoice-main-wrap {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .invoice-card-print {
            border: 1px solid #94a3b8 !important;
            box-shadow: none !important;
            padding: 1.5rem !important;
            background: #ffffff !important;
            border-radius: 6px !important;
        }
        .app-table th, .app-table td {
            color: #000000 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="invoice-main-wrap" style="max-width: 850px; margin: 0 auto;">
    <!-- Top Action Buttons -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <a href="{{ route('sales.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Sales
        </a>

        <div style="display: flex; gap: 0.5rem;">
            <!-- WhatsApp Share Button -->
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $sale->customer_phone ?? '');
                if ($cleanPhone && str_starts_with($cleanPhone, '03')) {
                    $cleanPhone = '92' . substr($cleanPhone, 1);
                }
                $waText = "*" . strtoupper($storeSettings['name']) . "*\nInvoice: " . $sale->invoice_number . "\nTotal: Rs." . number_format($sale->grand_total, 0) . "\nThank you for shopping!";
                $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text=" . urlencode($waText) : "https://api.whatsapp.com/send?text=" . urlencode($waText);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="btn btn-outline" style="color: #16a34a; border-color: #86efac;">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>

            <!-- Full Page A4 Print -->
            <button type="button" onclick="window.print()" class="btn btn-outline">
                <i class="fa-solid fa-file-invoice"></i> Print A4 Invoice
            </button>

            <!-- Thermal Print (Same Tab) -->
            <button type="button" onclick="window.printReceiptDirect({{ $sale->id }})" class="btn btn-primary" style="cursor: pointer;">
                <i class="fa-solid fa-print"></i> Print 80mm Slip
            </button>

            <!-- Return Items -->
            <a href="{{ route('returns.create', ['invoice_number' => $sale->invoice_number]) }}" class="btn btn-outline" style="color: #dc2626; border-color: #fca5a5;">
                <i class="fa-solid fa-arrow-rotate-left"></i> Return Items
            </a>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="card invoice-card-print" style="padding: 2rem;">
        <!-- Store Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gray-200); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <img src="{{ asset('images/logo.svg') }}" alt="Hashmi Grains Logo" style="height: 56px; width: auto; margin-bottom: 6px;">
                <p style="color: var(--gray-600); font-size: 0.85rem; margin-top: 2px;">{{ $storeSettings['address'] }}</p>
                <p style="color: var(--gray-600); font-size: 0.85rem;">Phone / WhatsApp: {{ $storeSettings['phone'] }}</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--gray-400); text-transform: uppercase;">INVOICE</div>
                <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: var(--gray-900);">{{ $sale->invoice_number }}</div>
                <div style="font-size: 0.85rem; color: var(--gray-600); margin-top: 4px;">Date: {{ $sale->created_at->format('d M Y, h:i A') }}</div>
                <div style="font-size: 0.85rem; color: var(--gray-600);">Cashier: {{ $sale->user->name ?? 'Staff' }}</div>
            </div>
        </div>

        <!-- Customer Details -->
        <div style="background: #f8fafc; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between;">
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Billed To:</div>
                <div style="font-weight: 700; color: var(--gray-900); font-size: 1rem;">{{ $sale->customer_name ?: 'Walk-in Customer' }}</div>
            </div>
            @if($sale->customer_phone)
                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Contact:</div>
                    <div style="font-weight: 700; color: #0f5132;">{{ $sale->customer_phone }}</div>
                </div>
            @endif
        </div>

        <!-- Line Items -->
        <div class="table-responsive" style="margin-bottom: 1.5rem;">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Item</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900);">{{ $item->product_name }}</div>
                            </td>
                            <td>Rs. {{ number_format($item->unit_price, 0) }} / {{ $item->unit }}</td>
                            <td style="font-weight: 700;">{{ $item->quantity }} {{ $item->unit }}</td>
                            <td style="font-family: 'Outfit'; font-weight: 700; text-align: right; color: var(--gray-900);">
                                Rs. {{ number_format($item->subtotal, 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Breakdown -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            @if($sale->returns && $sale->returns->count() > 0)
                <div style="flex: 1; min-width: 280px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: var(--radius-md); padding: 1rem;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: #991b1b; text-transform: uppercase; display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Return / Refund History
                    </div>
                    @foreach($sale->returns as $ret)
                        <div style="border-top: 1px dashed #fca5a5; padding-top: 0.5rem; margin-top: 0.5rem; font-size: 0.82rem;">
                            <div style="display: flex; justify-content: space-between; font-weight: 700;">
                                <span style="color: #991b1b;">{{ $ret->return_number }}</span>
                                <span style="color: #dc2626;">- Rs. {{ number_format($ret->total_refund, 0) }}</span>
                            </div>
                            <div style="color: var(--gray-600); font-size: 0.75rem; margin-top: 2px;">
                                {{ $ret->created_at->format('d M Y, h:i A') }} • Reason: {{ $ret->reason ?: 'Customer Return' }}
                            </div>
                            <div class="no-print" style="margin-top: 4px;">
                                <button type="button" onclick="window.printReturnReceiptDirect({{ $ret->id }})" class="btn btn-outline btn-sm" style="color: #991b1b; border-color: #fca5a5; font-size: 0.75rem; padding: 2px 8px;">
                                    <i class="fa-solid fa-print"></i> Print Return Slip
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div></div>
            @endif

            <div style="width: 320px;">
                <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; color: var(--gray-600); font-size: 0.9rem;">
                    <span>Subtotal:</span>
                    <span>Rs. {{ number_format($sale->subtotal, 0) }}</span>
                </div>
                @if($sale->discount > 0)
                    <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; color: #dc2626; font-size: 0.9rem;">
                        <span>Discount:</span>
                        <span>- Rs. {{ number_format($sale->discount, 0) }}</span>
                    </div>
                @endif
                <div style="display: flex; justify-content: space-between; padding: 0.6rem 0; border-top: 2px solid var(--gray-200); border-bottom: 2px solid var(--gray-200); margin: 0.5rem 0;">
                    <span style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 800;">Grand Total:</span>
                    <span style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: #0f5132;">Rs. {{ number_format($sale->grand_total, 0) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.85rem; color: var(--gray-600);">
                    <span>Payment Method:</span>
                    <span style="font-weight: 700; text-transform: capitalize;">{{ $sale->payment_method }}</span>
                </div>
                @if($sale->payment_method === 'cash')
                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.85rem; color: var(--gray-600);">
                        <span>Cash Paid:</span>
                        <span>Rs. {{ number_format($sale->cash_given, 0) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.9rem; font-weight: 700; color: #15803d;">
                        <span>Change Returned:</span>
                        <span>Rs. {{ number_format($sale->change_amount, 0) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Notice -->
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px dashed var(--gray-300); text-align: center; color: var(--gray-500); font-size: 0.8rem; line-height: 1.4;">
            @if(!empty($storeSettings['receipt_footer']))
                {!! nl2br(e($storeSettings['receipt_footer'])) !!}
            @else
                <div style="font-family: 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif; font-size: 0.95rem; font-weight: 700; color: #0f5132; margin-bottom: 4px;">تشریف آوری کا شکریہ • جزاک اللہ خیراً</div>
                <div>Thank you for shopping at {{ $storeSettings['name'] }}! Pure and authentic quality products.</div>
                <div style="font-size: 0.75rem; color: var(--gray-400); margin-top: 2px;">No return or exchange without original receipt within 3 days.</div>
            @endif
        </div>
    </div>
</div>
@endsection
