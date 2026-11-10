@extends('layouts.app')

@section('title', 'Product Returns & Exchanges - Hashmi Grains')
@section('header_title', 'Product Returns & Customer Refunds')

@section('content')
<div x-data="returnManager()">

    <!-- Stats and Action Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-left: 4px solid #0f5132;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Total Returns Processed</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #0f5132; margin-top: 4px;">
                {{ $totalReturnsCount }} Returns
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">Items returned & restocked</div>
        </div>

        <div class="card" style="padding: 1.25rem; border-left: 4px solid #dc2626;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Total Refund Amount</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 4px;">
                Rs. {{ number_format($totalRefundsAllTime, 0) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">Adjusted cash refunds</div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: center; align-items: stretch; background: #ecfdf5; border: 1px solid #a7f3d0;">
            <button type="button" @click="showReturnModal = true" class="btn btn-primary" style="font-size: 1rem; padding: 12px 20px; font-weight: 800; box-shadow: 0 4px 10px rgba(15, 81, 50, 0.3);">
                <i class="fa-solid fa-arrow-rotate-left"></i> Process Product Return (واپسی)
            </button>
        </div>
    </div>

    <!-- Returns History Table -->
    <div class="card">
        <div class="card-header" style="background: #f8fafc;">
            <h3 class="card-title">
                <i class="fa-solid fa-clock-rotate-left" style="color: #0f5132;"></i>
                <span>Returns & Exchanges History</span>
            </h3>
            <button type="button" @click="showReturnModal = true" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-plus"></i> New Return
            </button>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Return #</th>
                        <th>Original Invoice</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Returned Items</th>
                        <th>Total Refund</th>
                        <th>Handled By</th>
                        <th>Reason</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                        <tr>
                            <td>
                                <strong style="color: #991b1b; font-family: 'Outfit'; font-size: 0.95rem;">{{ $ret->return_number }}</strong>
                            </td>
                            <td>
                                @if($ret->sale)
                                    <a href="{{ route('sales.show', $ret->sale) }}" style="font-weight: 700; color: #0f5132; text-decoration: none;">
                                        {{ $ret->sale->invoice_number }}
                                    </a>
                                @else
                                    <span class="status-badge badge-info" style="font-size: 0.7rem;">Direct Return</span>
                                @endif
                            </td>
                            <td style="font-size: 0.82rem; color: var(--gray-600);">
                                {{ $ret->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900);">{{ $ret->customer_name ?: 'Walk-in' }}</div>
                            </td>
                            <td>
                                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.82rem;">
                                    @foreach($ret->items as $rItem)
                                        <li style="margin-bottom: 2px;">
                                            <strong>{{ $rItem->product_name }}</strong> &times; {{ $rItem->quantity }} 
                                            <span style="color: var(--gray-500); font-size: 0.75rem;">(Rs. {{ number_format($rItem->refund_amount, 0) }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 800; font-size: 1rem; color: #dc2626;">
                                Rs. {{ number_format($ret->total_refund, 0) }}
                            </td>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $ret->user->name ?? 'Staff' }}
                            </td>
                            <td style="font-size: 0.82rem; color: var(--gray-600);">
                                {{ $ret->reason ?: '-' }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                    <button type="button" onclick="window.printReturnReceiptDirect({{ $ret->id }})" class="btn btn-outline btn-sm" title="Quick Thermal Print" style="color: #991b1b; border-color: #fca5a5;">
                                        <i class="fa-solid fa-print"></i> Slip
                                    </button>
                                    <a href="{{ route('returns.receipt', $ret) }}" target="_blank" class="btn btn-outline btn-sm" title="Open Print Tab">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-arrow-rotate-left" style="font-size: 2.5rem; margin-bottom: 0.5rem; color: var(--gray-300);"></i>
                                <p style="font-weight: 700; font-size: 1rem; color: var(--gray-600);">No product returns recorded yet</p>
                                <button type="button" @click="showReturnModal = true" class="btn btn-primary btn-sm" style="margin-top: 0.5rem;">
                                    Process First Return
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.85rem; color: var(--gray-500);">
                    Showing <strong>{{ $returns->firstItem() }}</strong> to <strong>{{ $returns->lastItem() }}</strong> of <strong>{{ $returns->total() }}</strong> returns
                </div>
                <div>
                    {{ $returns->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- 1-Click Interactive Return Modal -->
    <div class="modal-overlay" x-show="showReturnModal" style="display: none;">
        <div class="modal-box" style="max-width: 720px;" @click.away="showReturnModal = false">
            <div class="modal-header" style="background: #fff1f2;">
                <h3 class="card-title">
                    <i class="fa-solid fa-arrow-rotate-left" style="color: #dc2626;"></i>
                    <span>Process Customer Return & Stock Restore</span>
                </h3>
                <button type="button" @click="showReturnModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--gray-500);">&times;</button>
            </div>

            <!-- Mode Switcher -->
            <div style="display: flex; border-bottom: 1px solid var(--gray-200); background: #f8fafc;">
                <button type="button" @click="returnMode = 'invoice'" 
                        :style="returnMode === 'invoice' ? 'background: white; border-bottom: 2px solid #0f5132; color: #0f5132; font-weight: 700;' : 'background: none; border: none; color: var(--gray-500);'"
                        style="flex: 1; padding: 12px; font-size: 0.9rem; cursor: pointer; border: none; outline: none;">
                    <i class="fa-solid fa-receipt"></i> 1. Return from Bill / Invoice (رسید سے واپسی)
                </button>
                <button type="button" @click="returnMode = 'direct'" 
                        :style="returnMode === 'direct' ? 'background: white; border-bottom: 2px solid #0f5132; color: #0f5132; font-weight: 700;' : 'background: none; border: none; color: var(--gray-500);'"
                        style="flex: 1; padding: 12px; font-size: 0.9rem; cursor: pointer; border: none; outline: none;">
                    <i class="fa-solid fa-box-open"></i> 2. Direct Product Return (بغیر رسید کے)
                </button>
            </div>

            <!-- Tab 1: Return from Invoice -->
            <div x-show="returnMode === 'invoice'">
                <form action="{{ route('returns.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_mode" value="invoice">
                    <input type="hidden" name="sale_id" :value="selectedSale ? selectedSale.id : ''">

                    <div class="modal-body">
                        <!-- Step 1: Select Bill -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; color: #0f5132;">
                                Step 1: Select Customer Invoice / Bill *
                            </label>
                            <select x-model="selectedSaleId" @change="onSelectSale()" class="form-control" style="font-size: 0.95rem; font-weight: 600;" required>
                                <option value="">-- Choose Invoice to Return --</option>
                                @foreach($recentSales as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->invoice_number }} &bull; {{ $s->customer_name ?: 'Walk-in' }} (Rs. {{ number_format($s->grand_total, 0) }} &bull; {{ $s->items->count() }} items) - {{ $s->created_at->format('d M, h:i A') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Line Items -->
                        <template x-if="selectedSale">
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--gray-700); text-transform: uppercase; margin-bottom: 0.5rem;">
                                    Step 2: Set Quantity to Return for Each Item:
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 250px; overflow-y: auto; margin-bottom: 1rem;">
                                    <template x-for="(item, idx) in selectedSale.items" :key="item.id">
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid var(--gray-200); border-radius: 8px; padding: 0.75rem 1rem;">
                                            <div>
                                                <input type="hidden" :name="'returns[' + idx + '][sale_item_id]'" :value="item.id">
                                                <div style="font-weight: 700; font-size: 0.92rem; color: var(--gray-900);" x-text="item.product_name"></div>
                                                <div style="font-size: 0.75rem; color: var(--gray-500);">
                                                    Billed: <strong x-text="item.quantity + ' ' + item.unit"></strong> &bull; Rate: Rs. <span x-text="item.unit_price"></span>
                                                </div>
                                            </div>

                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <!-- Plus / Minus Stepper -->
                                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                                    <button type="button" @click="decrementQty(item.id)" 
                                                            style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--gray-300); background: white; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fa-solid fa-minus" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        step="any" 
                                                        min="0" 
                                                        :max="item.quantity" 
                                                        :name="'returns[' + idx + '][quantity]'" 
                                                        x-model.number="returnQtys[item.id]" 
                                                        class="form-control" 
                                                        style="width: 60px; height: 32px; text-align: center; font-weight: 800; font-family: 'Outfit'; padding: 0;"
                                                    >
                                                    <button type="button" @click="incrementQty(item.id, item.quantity)" 
                                                            style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--gray-300); background: white; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fa-solid fa-plus" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                </div>

                                                <div style="width: 90px; text-align: right; font-family: 'Outfit'; font-weight: 800; color: #dc2626; font-size: 0.95rem;">
                                                    Rs. <span x-text="((returnQtys[item.id] || 0) * item.unit_price).toLocaleString()"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Live Refund Summary Banner -->
                                <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 0.85rem 1rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <span style="font-weight: 700; color: #991b1b; font-size: 0.9rem;">
                                        Total Refund to Customer (واپس رقم):
                                    </span>
                                    <span style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 900; color: #dc2626;">
                                        Rs. <span x-text="calcTotalRefund().toLocaleString()">0</span>
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Return Reason / Notes</label>
                                    <input type="text" name="reason" x-model="returnReason" class="form-control" placeholder="e.g. Returned sealed pack, customer wanted different product">
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="showReturnModal = false" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-danger" :disabled="!selectedSale || !hasSelectedItems()" style="font-weight: 800;">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Confirm Return & Refund
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Direct Product Return (No Bill) -->
            <div x-show="returnMode === 'direct'" style="display: none;">
                <form action="{{ route('returns.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_mode" value="direct">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Select Product to Return *</label>
                            <select name="product_id" x-model="directProductId" @change="updateDirectPrice()" class="form-control" required>
                                <option value="">-- Choose Product from Catalog --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Rs. {{ number_format($p->sale_price, 0) }} / {{ $p->unit }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div class="form-group">
                                <label class="form-label">Quantity to Return *</label>
                                <input type="number" step="any" min="0.01" name="quantity" x-model.number="directQty" @input="updateDirectPrice()" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Refund Amount (Rs) *</label>
                                <input type="number" step="any" min="0" name="refund_amount" x-model.number="directRefund" class="form-control" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div class="form-group">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" x-model="directCustomer" class="form-control" placeholder="Walk-in Customer">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Reason *</label>
                                <input type="text" name="reason" x-model="directReason" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="showReturnModal = false" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-danger" :disabled="!directProductId || directQty <= 0" style="font-weight: 800;">
                            <i class="fa-solid fa-arrow-rotate-left"></i> Confirm Direct Return & Restore Stock
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function returnManager() {
        return {
            showReturnModal: false,
            returnMode: 'invoice',
            selectedSaleId: '',
            salesList: {!! json_encode($recentSales) !!},
            productsList: {!! json_encode($products) !!},
            
            // Invoice mode state
            selectedSale: null,
            returnQtys: {},
            returnReason: 'Customer Return / Exchange',
            
            // Direct mode state
            directProductId: '',
            directQty: 1,
            directRefund: 0,
            directCustomer: '',
            directReason: 'Direct walk-in return',

            onSelectSale() {
                if (!this.selectedSaleId) {
                    this.selectedSale = null;
                    this.returnQtys = {};
                    return;
                }
                this.selectedSale = this.salesList.find(s => s.id == this.selectedSaleId);
                this.returnQtys = {};
                if (this.selectedSale && this.selectedSale.items) {
                    this.selectedSale.items.forEach(it => {
                        this.returnQtys[it.id] = 0;
                    });
                }
            },

            incrementQty(itemId, maxQty) {
                const current = parseFloat(this.returnQtys[itemId]) || 0;
                if (current < maxQty) {
                    this.returnQtys[itemId] = current + 1;
                }
            },

            decrementQty(itemId) {
                const current = parseFloat(this.returnQtys[itemId]) || 0;
                if (current > 0) {
                    this.returnQtys[itemId] = current - 1;
                }
            },

            calcTotalRefund() {
                if (!this.selectedSale || !this.selectedSale.items) return 0;
                let total = 0;
                this.selectedSale.items.forEach(it => {
                    const q = parseFloat(this.returnQtys[it.id]) || 0;
                    total += (q * it.unit_price);
                });
                return total;
            },

            hasSelectedItems() {
                return this.calcTotalRefund() > 0;
            },

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
