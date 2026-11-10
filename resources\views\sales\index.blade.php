@extends('layouts.app')

@section('title', 'Sales History - Hashmi Grains')
@section('header_title', 'Sales History & Past Invoices')

@section('content')
<div>
    <!-- Financial Summary Banner -->
    <div style="background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.25rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; box-shadow: var(--shadow-sm);">
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Filtered Total Revenue</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #0f5132;">
                Rs. {{ number_format($totalSalesAmount, 0) }}
            </div>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Filtered Net Profit</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #d97706;">
                Rs. {{ number_format($totalProfitAmount, 0) }}
            </div>
        </div>
        <div>
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Invoices Count</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: var(--gray-900);">
                {{ $sales->total() }} Bills
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 1.25rem;">
        <div class="card-body" style="padding: 1rem;">
            <form method="GET" action="{{ route('sales.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) auto; gap: 0.75rem; align-items: flex-end;">
                <div>
                    <label class="form-label">Search Invoice / Customer</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Invoice # or phone..." class="form-control">
                </div>

                <div>
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>

                <div>
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>

                <div>
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="">All Payment Methods</option>
                        <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ $paymentMethod === 'card' ? 'selected' : '' }}>Card / POS</option>
                        <option value="easypaisa" {{ $paymentMethod === 'easypaisa' ? 'selected' : '' }}>EasyPaisa</option>
                        <option value="online" {{ $paymentMethod === 'online' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="height: 40px;">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if($search || $dateFrom || $dateTo || $paymentMethod)
                        <a href="{{ route('sales.index') }}" class="btn btn-outline" style="height: 40px;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Items Billed</th>
                        <th>Payment</th>
                        <th>Grand Total</th>
                        <th>Net Profit</th>
                        <th>Cashier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: #0f5132; text-decoration: none;">
                                    {{ $sale->invoice_number }}
                                </a>
                                @if($sale->status === 'partial_return')
                                    <span class="status-badge badge-warning" style="font-size: 0.65rem; display: block; width: max-content; margin-top: 2px;">Returned Items</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $sale->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900);">{{ $sale->customer_name ?: 'Walk-in' }}</div>
                                @if($sale->customer_phone)
                                    <div style="font-size: 0.72rem; color: var(--gray-500);">
                                        <i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> {{ $sale->customer_phone }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $sale->items->count() }} items</span>
                            </td>
                            <td>
                                <span class="status-badge badge-info">{{ ucfirst($sale->payment_method) }}</span>
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 800; font-size: 1rem; color: #0f5132;">
                                Rs. {{ number_format($sale->grand_total, 0) }}
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 700; color: #d97706;">
                                Rs. {{ number_format($sale->profit, 0) }}
                            </td>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $sale->user->name ?? 'Staff' }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.35rem;">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline btn-sm" title="View Full Bill">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <button type="button" onclick="window.printReceiptDirect({{ $sale->id }})" class="btn btn-outline btn-sm" title="Thermal Print (Same Tab)">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                    <a href="{{ route('returns.create', ['invoice_number' => $sale->invoice_number]) }}" class="btn btn-outline btn-sm" style="color: #e11d48;" title="Return Item">
                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-receipt" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                <p style="font-weight: 600;">No sales matching criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div style="padding: 1rem; border-top: 1px solid var(--gray-200);">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
