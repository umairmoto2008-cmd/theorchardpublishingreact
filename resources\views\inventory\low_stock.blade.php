@extends('layouts.app')

@section('title', 'Low Stock Warnings - Hashmi Grains')
@section('header_title', 'Low Stock & Depleted Inventory Warnings')

@section('content')
<div x-data="{
    showRestockModal: false,
    restockForm: { id: '', name: '', unit: '', current_stock: '', quantity: '', purchase_price: '', notes: '' },
    openRestock(id, name, unit, current_stock, purchase_price) {
        this.restockForm = {
            id: id,
            name: name,
            unit: unit,
            current_stock: current_stock,
            quantity: '',
            purchase_price: purchase_price,
            notes: 'Restocked low inventory'
        };
        this.showRestockModal = true;
    }
}">
    <!-- Header Banner -->
    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 46px; height: 46px; border-radius: 50%; background: #e11d48; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #9f1239;">
                    {{ count($lowStockProducts) }} Items Below Minimum Alert Threshold
                </h3>
                <p style="font-size: 0.82rem; color: #be123c; margin-top: 2px;">
                    Order new supplies promptly to prevent sales disruption.
                </p>
            </div>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-outline" style="background: white;">
            <span>View All Catalog Items &rarr;</span>
        </a>
    </div>

    <!-- Table of low stock items -->
    <div class="card">
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Barcode</th>
                        <th>Current Remaining Stock</th>
                        <th>Alert Threshold</th>
                        <th>Purchase Cost</th>
                        <th>Sale Price</th>
                        <th>Urgent Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $p)
                        <tr style="background: #fffcfc;">
                            <td>
                                <div style="font-weight: 800; color: #991b1b; font-size: 0.92rem;">{{ $p->name }}</div>
                                <div style="font-size: 0.72rem; color: var(--gray-500);">Unit: <strong>{{ $p->unit }}</strong></div>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; font-weight: 600; color: #047857;">
                                    <i class="{{ $p->category->icon ?? 'fa-solid fa-box' }}" style="margin-right: 4px;"></i> {{ $p->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <code style="background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ $p->barcode }}</code>
                            </td>
                            <td>
                                <span class="status-badge badge-danger" style="font-size: 0.85rem; padding: 0.25rem 0.6rem;">
                                    {{ $p->stock_quantity }} {{ $p->unit }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--gray-600);">{{ $p->alert_quantity }} {{ $p->unit }}</span>
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 600; color: var(--gray-700);">
                                Rs. {{ number_format($p->purchase_price, 0) }}
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 800; color: #0f5132;">
                                Rs. {{ number_format($p->sale_price, 0) }}
                            </td>
                            <td>
                                <button 
                                    type="button"
                                    @click.stop="openRestock({{ $p->id }}, @js($p->name), @js($p->unit), {{ (float)$p->stock_quantity }}, {{ (float)$p->purchase_price }})" 
                                    class="btn btn-success btn-sm"
                                    style="box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);"
                                >
                                    <i class="fa-solid fa-plus-circle"></i> Restock Now
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #16a34a;">
                                <i class="fa-solid fa-circle-check" style="font-size: 3rem; margin-bottom: 0.75rem;"></i>
                                <h4 style="font-weight: 800; font-size: 1.1rem;">All Stock Levels Healthy!</h4>
                                <p style="font-size: 0.85rem; color: var(--gray-500); margin-top: 4px;">No products currently have low inventory warnings.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Restock Modal -->
    <div class="modal-overlay" x-show="showRestockModal" style="display: none;" @click.self="showRestockModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header" style="background: #ecfdf5;">
                <h3 class="card-title">
                    <i class="fa-solid fa-boxes-packing" style="color: #16a34a;"></i>
                    <span>Restock Low Inventory</span>
                </h3>
                <button type="button" @click="showRestockModal = false" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('products.restock.direct') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" :value="restockForm.id" x-model="restockForm.id">
                <div class="modal-body">
                    <div style="background: #f8fafc; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div style="font-weight: 700; font-size: 1rem; color: var(--gray-900);" x-text="restockForm.name"></div>
                        <div style="font-size: 0.82rem; color: #dc2626; margin-top: 2px;">
                            Current Depleted Stock: <strong x-text="restockForm.current_stock + ' ' + restockForm.unit"></strong>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Quantity to Add (<span x-text="restockForm.unit"></span>) *</label>
                            <input type="number" step="any" min="0.01" name="quantity" x-model="restockForm.quantity" class="form-control" placeholder="e.g. 50" required autofocus>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Purchase Price (Rs)</label>
                            <input type="number" step="any" min="0" name="purchase_price" x-model="restockForm.purchase_price" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Batch Notes</label>
                        <input type="text" name="notes" x-model="restockForm.notes" class="form-control" placeholder="e.g. Grain supplier restock">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showRestockModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check"></i> Confirm Restock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
