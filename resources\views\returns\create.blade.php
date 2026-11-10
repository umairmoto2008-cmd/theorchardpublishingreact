@extends('layouts.app')

@section('title', 'Process Product Return - Hashmi Grains')
@section('header_title', 'Process Product Return & Customer Refund')

@section('content')
<div x-data="returnCreatePage()" style="max-width: 900px; margin: 0 auto;">

    <!-- Top Mode Toggle -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <a href="{{ route('returns.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Returns History
        </a>

        <div style="display: flex; gap: 0.5rem; background: white; padding: 4px; border-radius: 10px; border: 1px solid var(--gray-300);">
            <button type="button" @click="returnType = 'invoice'" :class="returnType === 'invoice' ? 'btn btn-primary btn-sm' : 'btn btn-outline btn-sm'" style="border:none;">
                <i class="fa-solid fa-receipt"></i> Return with Invoice
            </button>
            <button type="button" @click="returnType = 'direct'" :class="returnType === 'direct' ? 'btn btn-primary btn-sm' : 'btn btn-outline btn-sm'" style="border:none;">
                <i class="fa-solid fa-box-open"></i> Direct Product Return (No Bill)
            </button>
        </div>
    </div>

    <!-- Mode 1: Return with Invoice -->
    <div x-show="returnType === 'invoice'">
        <!-- Search Invoice Box -->
        <div class="card" style="margin-bottom: 1.25rem;">
            <div class="card-header" style="background: #f8fafc;">
                <h3 class="card-title">
                    <i class="fa-solid fa-magnifying-glass" style="color: #059669;"></i>
                    <span>Find Customer Invoice</span>
                </h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <form method="GET" action="{{ route('returns.create') }}" style="display: flex; gap: 0.75rem;">
                    <div style="position: relative; flex: 1;">
                        <i class="fa-solid fa-barcode" style="position: absolute; left: 12px; top: 12px; color: var(--gray-400);"></i>
                        <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" placeholder="Enter Invoice # (e.g. HG-INV-6 or 6), customer name, or phone..." class="form-control" style="padding-left: 2.25rem; font-weight: 600;" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search Invoice
                    </button>
                </form>

                <!-- Quick Selection from Recent Invoices -->
                @if(count($recentSales) > 0 && !$sale)
                    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--gray-200);">
                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase; margin-bottom: 0.5rem;">
                            Or Select from Recent Completed Invoices:
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
                            @foreach($recentSales as $rs)
                                <a href="{{ route('returns.create', ['invoice_number' => $rs->invoice_number]) }}" 
                                   style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.85rem; border-radius: 8px; background: #f8fafc; border: 1px solid var(--gray-200); text-decoration: none; color: var(--gray-800); transition: all 0.2s;"
                                   onmouseover="this.style.background='#ecfdf5'; this.style.borderColor='#10b981';"
                                   onmouseout="this.style.background='#f8fafc'; this.style.borderColor='var(--gray-200)';"
                                >
                                    <div>
                                        <div style="font-weight: 700; color: #0f5132; font-size: 0.88rem;">{{ $rs->invoice_number }}</div>
                                        <div style="font-size: 0.72rem; color: var(--gray-500);">{{ $rs->customer_name ?: 'Walk-in' }} &bull; {{ $rs->items->count() }} items</div>
                                    </div>
                                    <div style="font-family: 'Outfit'; font-weight: 800; font-size: 0.95rem; color: #059669;">
                                        Rs. {{ number_format($rs->grand_total, 0) }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($invoiceNumber && !$sale)
            <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>No invoice found matching "<strong>{{ $invoiceNumber }}</strong>". Try entering the invoice number or select from recent bills above.</span>
            </div>
        @endif

        @if($sale)
            <!-- Invoice Loaded: Select items to return -->
            <div class="card">
                <div class="card-header" style="background: #ecfdf5; border-bottom: 1px solid #a7f3d0;">
                    <div>
                        <h3 class="card-title">
                            <i class="fa-solid fa-file-invoice" style="color: #0f5132;"></i>
                            <span>Invoice: {{ $sale->invoice_number }}</span>
                        </h3>
                        <div style="font-size: 0.8rem; color: #047857; margin-top: 2px;">
                            Billed on {{ $sale->created_at->format('d M Y, h:i A') }} to <strong>{{ $sale->customer_name ?: 'Walk-in Customer' }}</strong>
                        </div>
                    </div>
                    <span style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 800; color: #0f5132;">
                        Total: Rs. {{ number_format($sale->grand_total, 0) }}
                    </span>
                </div>

                <form action="{{ route('returns.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_mode" value="invoice">
                    <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                    <div class="card-body" style="padding: 1.25rem;">
                        <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 1rem;">
                            Enter the <strong>Return Qty</strong> for the items the customer is returning. Stock will be restored to inventory automatically.
                        </p>

                        <div class="table-responsive" style="margin-bottom: 1.25rem;">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Item Name</th>
                                        <th style="width: 15%;">Unit Price</th>
                                        <th style="width: 15%;">Original Billed</th>
                                        <th style="width: 15%;">Return Qty</th>
                                        <th style="width: 15%; text-align: right;">Refund Preview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $idx => $item)
                                        <tr x-data="{ returnQty: 0, price: {{ $item->unit_price }} }">
                                            <td>
                                                <input type="hidden" name="returns[{{ $idx }}][sale_item_id]" value="{{ $item->id }}">
                                                <div style="font-weight: 700; color: var(--gray-900);">{{ $item->product_name }}</div>
                                            </td>
                                            <td style="font-family: 'Outfit'; font-weight: 600; color: var(--gray-700);">
                                                Rs. {{ number_format($item->unit_price, 0) }} / {{ $item->unit }}
                                            </td>
                                            <td style="font-weight: 700; color: var(--gray-700);">
                                                {{ $item->quantity }} {{ $item->unit }}
                                            </td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    step="any" 
                                                    min="0" 
                                                    max="{{ $item->quantity }}" 
                                                    name="returns[{{ $idx }}][quantity]" 
                                                    x-model.number="returnQty"
                                                    class="form-control" 
                                                    style="width: 90px; font-weight: 800; font-family: 'Outfit'; color: #0f5132;"
                                                    placeholder="0"
                                                >
                                            </td>
                                            <td style="font-family: 'Outfit'; font-weight: 800; color: #dc2626; text-align: right; font-size: 0.95rem;">
                                                Rs. <span x-text="(returnQty * price).toLocaleString()">0</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Reason for Return / Exchange *</label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Customer returned sealed pack, exchanged for rice" required>
                        </div>
                    </div>

                    <div class="card-footer" style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-top: 1px solid var(--gray-200);">
                        <a href="{{ route('returns.create') }}" class="btn btn-outline">Change Invoice</a>
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Confirm Return & Refund
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Mode 2: Direct Product Return (No Invoice) -->
    <div x-show="returnType === 'direct'" style="display: none;">
        <div class="card">
            <div class="card-header" style="background: #f8fafc;">
                <h3 class="card-title">
                    <i class="fa-solid fa-box-open" style="color: #2563eb;"></i>
                    <span>Direct Product Return (Walk-in Return without Bill)</span>
                </h3>
            </div>
            <form action="{{ route('returns.store') }}" method="POST">
                @csrf
                <input type="hidden" name="return_mode" value="direct">

                <div class="card-body" style="padding: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Select Product to Return *</label>
                        <select name="product_id" x-model="directProductId" @change="updateDirectPrice()" class="form-control" required>
                            <option value="">-- Choose Product from Catalog --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Sale Price: Rs. {{ number_format($p->sale_price, 0) }} / {{ $p->unit }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Quantity to Return *</label>
                            <input type="number" step="any" min="0.01" name="quantity" x-model.number="directQty" @input="updateDirectPrice()" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Refund Amount to Customer (Rs) *</label>
                            <input type="number" step="any" min="0" name="refund_amount" x-model.number="directRefund" class="form-control" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Optional customer name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reason for Return *</label>
                            <input type="text" name="reason" class="form-control" placeholder="e.g. Unopened item returned by regular customer" required>
                        </div>
                    </div>
                </div>

                <div class="card-footer" style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-top: 1px solid var(--gray-200);">
                    <a href="{{ route('returns.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Confirm Direct Return & Restore Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function returnCreatePage() {
        return {
            returnType: 'invoice',
            directProductId: '',
            directQty: 1,
            directRefund: 0,
            productsList: {!! json_encode($products) !!},
            
            updateDirectPrice() {
                const prod = this.productsList.find(p => p.id == this.directProductId);
                if (prod) {
                    this.directRefund = (prod.sale_price * this.directQty);
                } else {
                    this.directRefund = 0;
                }
            }
        };
    }
</script>
@endsection
