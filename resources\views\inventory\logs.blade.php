@extends('layouts.app')

@section('title', 'Live Inventory Manager & Stock Editor - Hashmi Grains')
@section('header_title', 'Inventory Manager & Quick Stock Editor')

@section('styles')
<style>
    /* Tabs switcher */
    .inventory-nav-tabs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #e2e8f0;
        padding: 4px;
        border-radius: 10px;
        width: fit-content;
    }

    .inv-tab-btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 0.84rem;
        font-weight: 700;
        border: none;
        background: transparent;
        color: var(--gray-600);
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .inv-tab-btn.active {
        background: white;
        color: #0f5132;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Filter Bar */
    .search-filter-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }

    /* Master Inventory Editor Table */
    .inventory-editor-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }

    .inventory-editor-table th {
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #475569;
        padding: 1.15rem 1rem;
        border-bottom: 1.5px solid #e2e8f0;
        background: #ffffff;
        white-space: nowrap;
    }

    .inventory-editor-table td {
        padding: 1.1rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .inventory-editor-table tr:hover td {
        background: #f8fafc;
    }

    /* Product Name & SKU */
    .product-name-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.92rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
        line-height: 1.25;
    }

    .product-sku-sub {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        font-family: 'Outfit', sans-serif;
        margin-top: 3px;
    }

    /* Category */
    .product-category-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    /* Sale Price */
    .price-sale {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.95rem;
        white-space: nowrap;
    }

    /* Current Stock */
    .stock-val-normal {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        color: #0f172a;
        white-space: nowrap;
    }

    .stock-val-low {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        color: #dc2626;
        white-space: nowrap;
    }

    /* Inline Stock Form */
    .inline-stock-form {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .stock-input-box {
        width: 76px;
        height: 34px;
        text-align: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        color: #0f172a;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 4px;
        transition: all 0.2s ease;
    }

    .stock-input-box:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        outline: none;
    }

    .btn-save-stock {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        height: 34px;
        padding: 0 13px;
        font-size: 0.8rem;
        font-weight: 700;
        color: white;
        background: #0284c7;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.25);
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .btn-save-stock:hover {
        background: #0369a1;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(2, 132, 199, 0.3);
    }

    .btn-save-stock:active {
        transform: translateY(0);
    }

    .alert-input-box {
        width: 54px;
        height: 34px;
        text-align: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        color: #dc2626;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 4px;
        transition: all 0.2s ease;
    }

    .alert-input-box:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
        outline: none;
    }

    .updated-by-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    /* Print Label Pill */
    .btn-print-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 32px;
        padding: 0 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        background: #f0fdf4;
        color: #16a34a;
        border: 1.5px solid #86efac;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .btn-print-pill:hover {
        background: #dcfce7;
        color: #15803d;
        border-color: #4ade80;
        transform: translateY(-1px);
    }

    /* Quick Step (+/-) Buttons */
    .btn-quick-step {
        min-width: 36px;
        height: 32px;
        padding: 0 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-size: 0.78rem;
        font-weight: 800;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-step-plus {
        background: #f0fdf4;
        color: #16a34a;
        border: 1.5px solid #86efac;
    }

    .btn-step-plus:hover {
        background: #dcfce7;
        transform: translateY(-1px);
    }

    .btn-step-minus {
        background: #fef2f2;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
    }

    .btn-step-minus:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }

    /* Barcode Column & SVG */
    .barcode-col-td {
        width: 140px;
        min-width: 130px;
        max-width: 160px;
        text-align: center;
    }

    .table-barcode-svg {
        max-width: 135px !important;
        height: 38px !important;
        display: block !important;
        margin: 0 auto !important;
    }

    /* Floating Toast Notification */
    #liveToastBox {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 9999;
        transition: opacity 0.3s ease;
    }
</style>
@endsection

@section('content')
<div x-data="{ 
    activeTab: 'editor',
    showAdjustModal: false,
    showLabelModal: false,
    previewBarcodeDataUrl: '',
    labelModal: { id: '', name: '', barcode: '', price: '0', unit: 'Kg', headerText: 'HASHMI GRAINS', customText: '', copies: 1, size: 'slip' },

    openPrintModal(id, name, barcode, price, unit, expiry_date, category_name) {
        const cleanBarcode = (barcode && String(barcode).trim().length > 0) ? String(barcode).trim() : String(id).padStart(6, '0');
        this.labelModal = {
            id: id,
            name: name,
            barcode: cleanBarcode,
            price: Number(price).toLocaleString('en-US'),
            unit: unit || 'Kg',
            headerText: (category_name ? String(category_name).toUpperCase() : 'HASHMI GRAINS'),
            customText: (expiry_date ? 'Exp: ' + expiry_date : ''),
            copies: 1,
            size: 'slip'
        };
        try {
            if (typeof generateBarcodeBase64 === 'function') {
                this.previewBarcodeDataUrl = generateBarcodeBase64(cleanBarcode, 45);
            }
        } catch(e) {
            console.error('Barcode preview error:', e);
        }
        this.showLabelModal = true;
    },

    setPresetCopies(cnt) {
        this.labelModal.copies = cnt;
    },

    updateCopiesDelta(d) {
        let cur = parseInt(this.labelModal.copies) || 1;
        this.labelModal.copies = Math.max(1, Math.min(500, cur + d));
    },

    triggerPrintLabels() {
        const url = (window.APP_BASE_URL || '') + '/products/barcodes/print?product_id=' + encodeURIComponent(this.labelModal.id) + '&copies=' + encodeURIComponent(this.labelModal.copies) + '&custom=' + encodeURIComponent(this.labelModal.customText || '') + '&auto=1';
        window.open(url, '_blank');
        this.showLabelModal = false;
    }
}">
    <!-- Floating Live Update Toast -->
    <div id="liveToastBox">
        <i class="fa-solid fa-circle-check" style="color: #4ade80;"></i>
        <span id="liveToastMsg">Stock updated successfully</span>
    </div>

    <!-- Top Action & Tabs Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <!-- Left: Navigation Tabs -->
        <div class="inventory-nav-tabs">
            <button type="button" @click="activeTab = 'editor'" :class="activeTab === 'editor' ? 'inv-tab-btn active' : 'inv-tab-btn'">
                <i class="fa-solid fa-bolt" style="color: #0284c7;"></i>
                <span>Quick Stock Editor</span>
            </button>
            <button type="button" @click="activeTab = 'history'" :class="activeTab === 'history' ? 'inv-tab-btn active' : 'inv-tab-btn'">
                <i class="fa-solid fa-clock-rotate-left" style="color: #d97706;"></i>
                <span>Movement Audit Logs</span>
            </button>
        </div>

        <!-- Right: Actions -->
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('products.index') }}" class="btn btn-outline" style="background: white;">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Products Directory</span>
            </a>
            <button @click="showAdjustModal = true" class="btn btn-primary">
                <i class="fa-solid fa-sliders"></i>
                <span>Manual Adjustment</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('inventory.logs') }}" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 0.75rem; align-items: flex-end;">
            <div>
                <label class="form-label">Search Product / Barcode</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: var(--gray-400);"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, barcode..." class="form-control" style="padding-left: 2.25rem;">
                </div>
            </div>

            <div>
                <label class="form-label">Filter by Category</label>
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.4rem;">
                <button type="submit" class="btn btn-primary" style="height: 40px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if($search || $categoryId || $productId || $type)
                    <a href="{{ route('inventory.logs') }}" class="btn btn-outline" style="height: 40px;" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TAB 1: QUICK STOCK EDITOR TABLE (Matching Reference Image) -->
    <div x-show="activeTab === 'editor'" class="card" style="border: 1px solid var(--gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); background: white;">
        <div class="table-responsive">
            <table class="inventory-editor-table">
                <thead>
                    <tr>
                        <th style="min-width: 190px;">PART NAME</th>
                        <th style="width: 125px;">CATEGORY</th>
                        <th style="width: 100px;">SALE PRICE</th>
                        <th style="width: 140px; text-align: center;">BARCODE</th>
                        <th style="width: 95px;">CURRENT STOCK</th>
                        <th style="width: 180px;">STOCK EDIT & SINGLE SAVE</th>
                        <th style="width: 80px; text-align: center;">MIN ALERT</th>
                        <th style="width: 135px;">LAST UPDATED BY</th>
                        <th style="width: 115px; text-align: center;">PRINT</th>
                        <th style="width: 100px; text-align: center;">QUICK ADJUST (+/-)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr id="product-row-{{ $p->id }}">
                            <!-- Product / Part Name -->
                            <td>
                                <div class="product-name-title">{{ $p->name }}</div>
                                <div class="product-sku-sub">SKU: {{ $p->barcode }}</div>
                            </td>

                            <!-- Category -->
                            <td>
                                <div class="product-category-text">{{ $p->category->name ?? 'General' }}</div>
                            </td>

                            <!-- Sale Price -->
                            <td>
                                <div class="price-sale">Rs {{ number_format($p->sale_price, 0) }}</div>
                            </td>

                            <!-- Barcode -->
                            <td class="barcode-col-td">
                                <svg class="table-barcode-svg" 
                                     jsbarcode-value="{{ $p->barcode }}" 
                                     jsbarcode-format="CODE128" 
                                     jsbarcode-height="26" 
                                     jsbarcode-width="1.1" 
                                     jsbarcode-displayValue="true" 
                                     jsbarcode-fontSize="10" 
                                     jsbarcode-font="Outfit, monospace"
                                     jsbarcode-textMargin="2" 
                                     jsbarcode-margin="0"></svg>
                                @if($p->expiry_date)
                                    <div style="font-size: 0.68rem; font-weight: 700; color: {{ $p->expiry_date->isPast() ? '#dc2626' : '#64748b' }}; margin-top: 3px; font-family: 'Outfit', sans-serif;">
                                        <i class="fa-regular fa-calendar" style="font-size: 0.65rem;"></i> Exp: {{ $p->expiry_date->format('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Current Stock -->
                            <td>
                                @php
                                    $isLow = $p->stock_quantity <= 0 || $p->isLowStock();
                                    $formattedStock = (float)$p->stock_quantity == (int)$p->stock_quantity ? (int)$p->stock_quantity : (float)$p->stock_quantity;
                                @endphp
                                <strong id="current-stock-label-{{ $p->id }}" class="{{ $isLow ? 'stock-val-low' : 'stock-val-normal' }}">
                                    {{ $formattedStock }} units
                                </strong>
                            </td>

                            <!-- Stock Edit & Single Save -->
                            <td>
                                <form action="{{ route('inventory.quick_update') }}" method="POST" class="inline-stock-form ajax-stock-form" id="stock-form-{{ $p->id }}" data-product-id="{{ $p->id }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <input type="number" step="any" min="0" name="stock_quantity" id="input-stock-{{ $p->id }}" value="{{ $formattedStock }}" class="stock-input-box" required>
                                    <button type="submit" class="btn-save-stock" title="Save this stock value">
                                        <i class="fa-solid fa-check"></i> Save
                                    </button>
                                </form>
                            </td>

                            <!-- Min Alert -->
                            <td style="text-align: center;">
                                @php
                                    $formattedAlert = (float)$p->alert_quantity == (int)$p->alert_quantity ? (int)$p->alert_quantity : (float)$p->alert_quantity;
                                @endphp
                                <input type="number" step="any" min="1" name="alert_quantity" id="input-alert-{{ $p->id }}" value="{{ $formattedAlert }}" form="stock-form-{{ $p->id }}" class="alert-input-box" title="Minimum Stock Alert Threshold">
                            </td>

                            <!-- Last Updated By -->
                            <td>
                                <span class="updated-by-text" id="updated-by-{{ $p->id }}">
                                    {{ $p->inventoryLogs->first()->user->name ?? 'Admin Manager' }}
                                </span>
                            </td>

                            <!-- Print Label -->
                            <td style="text-align: center;">
                                <button type="button" @click="openPrintModal({{ $p->id }}, @js($p->name), @js($p->barcode ?? ''), {{ (float)$p->sale_price }}, @js($p->unit), @js($p->expiry_date ? $p->expiry_date->format('d/m/Y') : ''), @js($p->category->name ?? 'Hashmi Grains'))" class="btn-print-pill" title="Print Barcode Label for {{ $p->name }}">
                                    <i class="fa-solid fa-barcode"></i> Print Label
                                </button>
                            </td>

                            <!-- Quick Adjust (+/-) -->
                            <td style="text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 5px; justify-content: center;">
                                    <!-- +10 Form -->
                                    <form action="{{ route('inventory.quick_adjust') }}" method="POST" class="ajax-adjust-form" data-product-id="{{ $p->id }}" style="margin: 0; display: inline;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $p->id }}">
                                        <input type="hidden" name="delta" value="10">
                                        <button type="submit" class="btn-quick-step btn-step-plus" title="Quick Add +10 Units">+10</button>
                                    </form>

                                    <!-- -10 Form -->
                                    <form action="{{ route('inventory.quick_adjust') }}" method="POST" class="ajax-adjust-form" data-product-id="{{ $p->id }}" style="margin: 0; display: inline;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $p->id }}">
                                        <input type="hidden" name="delta" value="-10">
                                        <button type="submit" class="btn-quick-step btn-step-minus" title="Quick Deduct -10 Units">-10</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-box-open" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                <p style="font-weight: 600;">No products found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.85rem; color: var(--gray-500);">
                    Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> items
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- TAB 2: MOVEMENT AUDIT LOGS HISTORY TABLE -->
    <div x-show="activeTab === 'history'" style="display: none;" class="card" style="border: 1px solid var(--gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); background: white;">
        <div class="table-responsive">
            <table class="inventory-editor-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date & Time</th>
                        <th style="width: 24%;">Product</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 15%;">Stock Balance</th>
                        <th style="width: 10%;">Reference</th>
                        <th style="width: 10%;">Handled By</th>
                        <th style="width: 14%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $log->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900);">
                                    {{ $log->product->name ?? 'Deleted Product' }}
                                </div>
                            </td>
                            <td>
                                @if($log->type === 'purchase_inward')
                                    <span class="status-badge badge-success"><i class="fa-solid fa-arrow-up" style="font-size: 0.7rem; margin-right: 2px;"></i> Inward</span>
                                @elseif($log->type === 'sale_outward')
                                    <span class="status-badge badge-info"><i class="fa-solid fa-arrow-down" style="font-size: 0.7rem; margin-right: 2px;"></i> Sale</span>
                                @elseif($log->type === 'return_inward')
                                    <span class="status-badge badge-warning"><i class="fa-solid fa-rotate-left" style="font-size: 0.7rem; margin-right: 2px;"></i> Return</span>
                                @elseif($log->type === 'damage_adjustment')
                                    <span class="status-badge badge-danger"><i class="fa-solid fa-triangle-exclamation" style="font-size: 0.7rem; margin-right: 2px;"></i> Damage</span>
                                @else
                                    <span class="status-badge badge-info"><i class="fa-solid fa-sliders" style="font-size: 0.7rem; margin-right: 2px;"></i> Audit</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-family: 'Outfit'; font-weight: 800; font-size: 1.05rem; color: #0f5132;">
                                    {{ (float)$log->new_stock }}
                                </div>
                                <div style="font-size: 0.73rem; color: var(--gray-500); margin-top: 2px;">
                                    <span style="font-weight: 700; color: {{ in_array($log->type, ['sale_outward', 'damage_adjustment']) ? '#dc2626' : '#16a34a' }};">
                                        {{ in_array($log->type, ['sale_outward', 'damage_adjustment']) ? '-' : '+' }}{{ (float)$log->quantity }}
                                    </span> 
                                    <span style="color: var(--gray-400);">({{ (float)$log->old_stock }} &rarr; {{ (float)$log->new_stock }})</span>
                                </div>
                            </td>
                            <td>
                                @if($log->reference_id)
                                    <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; color: #0f5132;">{{ $log->reference_id }}</code>
                                @else
                                    <span style="color: var(--gray-400);">-</span>
                                @endif
                            </td>
                            <td style="font-size: 0.82rem; color: var(--gray-700);">
                                {{ $log->user->name ?? 'System' }}
                            </td>
                            <td style="font-size: 0.82rem; color: var(--gray-600);">
                                {{ $log->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-clock-rotate-left" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                <p style="font-weight: 600;">No movement history logged yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.85rem; color: var(--gray-500);">
                    Showing <strong>{{ $logs->firstItem() }}</strong> to <strong>{{ $logs->lastItem() }}</strong> of <strong>{{ $logs->total() }}</strong> entries
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal: Manual Stock Adjustment -->
    <div class="modal-overlay" x-show="showAdjustModal" style="display: none;" @click.self="showAdjustModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header" style="background: #ecfdf5;">
                <h3 class="card-title">
                    <i class="fa-solid fa-sliders" style="color: #059669;"></i>
                    <span>Manual Stock Adjustment</span>
                </h3>
                <button type="button" @click="showAdjustModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--gray-500);">&times;</button>
            </div>
            <form action="{{ route('inventory.adjust') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Select Product *</label>
                        <select name="product_id" class="form-control" required>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">
                                    {{ $prod->name }} (Current Stock: {{ $prod->stock_quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Adjustment Action *</label>
                            <select name="type" class="form-control" required>
                                <option value="purchase_inward">Add Stock / Inward (+)</option>
                                <option value="damage_adjustment">Deduct / Damage / Loss (-)</option>
                                <option value="set_exact">Set Exact Shelf Count (=)</option>
                                <option value="manual_edit">Inventory Audit Correction (+)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quantity *</label>
                            <input type="number" step="any" min="0" name="quantity" class="form-control" placeholder="e.g. 10 or exact count" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adjustment Reason / Notes *</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Physical stock count correction or supplier delivery" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showAdjustModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Stock Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Print Barcode Labels (Hashmi Green Theme & Bulletproof Direct Print) -->
    <div class="modal-overlay" x-show="showLabelModal" style="display: none;" @click.self="showLabelModal = false" x-transition.opacity>
        <div class="modal-box" style="max-width: 460px; width: 95%; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(6,78,59,0.35); background: #ffffff; border: 2px solid #a7f3d0;" @click.stop>
            
            <!-- Modal Header: Emerald Gradient -->
            <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 60%, #059669 100%); padding: 1.15rem 1.35rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #059669;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 1.15rem;">
                        <i class="fa-solid fa-barcode"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #ffffff; line-height: 1.2;">
                            Print Barcode Labels
                        </h3>
                        <p style="margin: 0; font-size: 0.72rem; color: #d1fae5; font-weight: 600;">
                            بارکوڈ پرنٹ (Thermal Slip Paper - خودکار کٹ)
                        </p>
                    </div>
                </div>
                <button type="button" @click="showLabelModal = false" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.15); border: none; font-size: 1.3rem; cursor: pointer; color: #ffffff; display: flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" style="padding: 1.25rem; background: #ffffff;">
                
                <!-- Live Sticker Preview Card in Green Theme -->
                <div style="background: #f0fdf4; border: 2px dashed #10b981; border-radius: 14px; padding: 14px 12px; text-align: center; margin-bottom: 1.15rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.08); position: relative;">
                    <div style="position: absolute; top: -10px; right: 12px; background: #059669; color: white; font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.04em; text-transform: uppercase;">
                        Thermal Slip Preview (سلپ پریویو)
                    </div>
                    
                    <div style="font-size: 0.72rem; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;" x-text="labelModal.headerText || 'HASHMI GRAINS'">
                        HASHMI GRAINS
                    </div>
                    <div style="font-size: 0.92rem; font-weight: 800; color: #0f172a; text-transform: uppercase; margin: 3px 0 5px; line-height: 1.2;" x-text="labelModal.name">
                        PRODUCT NAME
                    </div>
                    
                    <!-- Real Canvas-Generated Barcode Image + Monospace Barcode Text -->
                    <div style="margin: 5px auto; display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 5px 10px; border-radius: 8px; border: 1px solid #bbf7d0; width: fit-content; max-width: 95%;">
                        <img :src="previewBarcodeDataUrl" style="height: 38px; max-width: 220px; display: block; margin: 0 auto; image-rendering: pixelated;" alt="Barcode Preview" />
                        <div style="font-family: 'Outfit', monospace; font-size: 0.78rem; font-weight: 800; color: #0f172a; letter-spacing: 0.08em; margin-top: 3px; line-height: 1;" x-text="labelModal.barcode"></div>
                    </div>
                    
                    <div style="font-family: 'Outfit', sans-serif; font-size: 1.3rem; font-weight: 900; color: #047857; margin-top: 4px;" x-text="'Rs ' + labelModal.price + (labelModal.unit ? ' / ' + labelModal.unit : '')">
                        Rs 0
                    </div>
                    
                    <template x-if="labelModal.customText">
                        <div style="font-size: 0.78rem; font-weight: 800; color: #065f46; margin-top: 5px; padding: 3px 10px; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 6px; display: inline-block;" x-text="labelModal.customText"></div>
                    </template>
                </div>

                <!-- Number of Labels to Print & Presets -->
                <div class="form-group" style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 0.85rem; font-weight: 800; color: #065f46; margin: 0; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-copy" style="color: #059669;"></i>
                            <span>Number of Slips to Print (تعداد سلپ):</span>
                        </label>
                        <div style="display: flex; gap: 4px;">
                            <button type="button" @click="setPresetCopies(1)" style="padding: 2px 7px; border-radius: 5px; border: 1px solid #a7f3d0; background: #ecfdf5; font-size: 0.72rem; font-weight: 700; color: #065f46; cursor: pointer;">1x</button>
                            <button type="button" @click="setPresetCopies(2)" style="padding: 2px 7px; border-radius: 5px; border: 1px solid #a7f3d0; background: #ecfdf5; font-size: 0.72rem; font-weight: 700; color: #065f46; cursor: pointer;">2x</button>
                            <button type="button" @click="setPresetCopies(5)" style="padding: 2px 7px; border-radius: 5px; border: 1px solid #a7f3d0; background: #ecfdf5; font-size: 0.72rem; font-weight: 700; color: #065f46; cursor: pointer;">5x</button>
                            <button type="button" @click="setPresetCopies(10)" style="padding: 2px 7px; border-radius: 5px; border: 1px solid #a7f3d0; background: #ecfdf5; font-size: 0.72rem; font-weight: 700; color: #065f46; cursor: pointer;">10x</button>
                            <button type="button" @click="setPresetCopies(20)" style="padding: 2px 7px; border-radius: 5px; border: 1px solid #a7f3d0; background: #ecfdf5; font-size: 0.72rem; font-weight: 700; color: #065f46; cursor: pointer;">20x</button>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; border: 1.5px solid #6ee7b7; border-radius: 10px; overflow: hidden; background: #f0fdf4;">
                        <button type="button" @click="updateCopiesDelta(-1)" style="width: 44px; height: 42px; border: none; background: #ecfdf5; color: #065f46; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">-</button>
                        <input type="number" min="1" max="500" x-model.number="labelModal.copies" style="flex: 1; border: none; border-left: 1px solid #a7f3d0; border-right: 1px solid #a7f3d0; height: 42px; text-align: center; font-size: 1.2rem; font-weight: 800; font-family: 'Outfit'; color: #064e3b; background: #ffffff; outline: none;">
                        <button type="button" @click="updateCopiesDelta(1)" style="width: 44px; height: 42px; border: none; background: #ecfdf5; color: #065f46; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">+</button>
                    </div>
                </div>

                <!-- Custom Bottom Text Option -->
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 800; color: #065f46; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-tag" style="color: #059669;"></i>
                            <span>Custom Bottom Text (نیچے کسٹم ٹیکسٹ):</span>
                        </span>
                        <span style="font-size: 0.72rem; color: #059669; font-weight: 700;">Live Preview</span>
                    </label>
                    <input type="text" x-model="labelModal.customText" class="form-control" placeholder="مثلاً Exp: 12/2026، Fresh Batch، No Return" style="height: 42px; border-radius: 10px; font-size: 0.88rem; border: 1.5px solid #a7f3d0; background: #ffffff;">
                </div>
            </div>

            <!-- Modal Footer: Green Action -->
            <div class="modal-footer" style="background: #ecfdf5; border-top: 1.5px solid #a7f3d0; padding: 1rem 1.35rem; display: flex; gap: 0.75rem; justify-content: flex-end; align-items: center;">
                <button type="button" @click="showLabelModal = false" class="btn btn-outline" style="background: white; border: 1.5px solid #cbd5e1; color: #475569; font-weight: 700; border-radius: 10px; padding: 9px 18px;">
                    Cancel
                </button>
                <button type="button" @click="triggerPrintLabels()" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; font-weight: 800; font-size: 0.95rem; border: none; border-radius: 10px; padding: 10px 24px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35); cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 18px rgba(5,150,105,0.45)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 14px rgba(5,150,105,0.35)'">
                    <i class="fa-solid fa-print"></i>
                    <span>Print Barcodes</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    function renderInventoryBarcodes() {
        if (typeof JsBarcode === 'function') {
            try {
                JsBarcode('.table-barcode-svg').init();
            } catch(e) {
                console.warn('Barcode render notice:', e);
            }
        }
    }

    // Helper: generate high-DPI Base64 PNG image of barcode BARS ONLY (displayValue: false)
    function generateBarcodeBase64(barcodeVal, heightPx = 45) {
        const val = String(barcodeVal || '000000').trim();
        const canvas = document.createElement('canvas');
        try {
            JsBarcode(canvas, val, {
                format: "CODE128",
                width: 2,
                height: heightPx,
                displayValue: false,
                margin: 0,
                background: "#ffffff",
                lineColor: "#000000"
            });
            return canvas.toDataURL("image/png");
        } catch(e) {
            console.warn("CODE128 barcode fallback:", e);
            try {
                JsBarcode(canvas, val, {
                    width: 2,
                    height: heightPx,
                    displayValue: false,
                    margin: 0,
                    background: "#ffffff",
                    lineColor: "#000000"
                });
                return canvas.toDataURL("image/png");
            } catch(err2) {
                console.error("Barcode canvas error:", err2);
                const ctx = canvas.getContext('2d');
                canvas.width = 180;
                canvas.height = heightPx;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';
                ctx.fillRect(10, 5, 160, heightPx - 10);
                return canvas.toDataURL("image/png");
            }
        }
    }

    function printCustomLabels(data) {
        const printWin = window.open('', '_blank', 'width=850,height=650');
        if (!printWin) {
            alert('Please allow popups for printing barcode labels.');
            return;
        }

        const copies = Math.max(1, parseInt(data.copies) || 1);
        const barcodeImg = generateBarcodeBase64(data.barcode, 36);
        const hasCustomText = Boolean(data.customText && String(data.customText).trim().length > 0);
        
        // Exact 50mm x 30mm Standard Label Printer Roll Dimensions
        const pageWidthMm = 50;
        const pageHeightMm = 30;

        let slipsHtml = '';
        for (let i = 0; i < copies; i++) {
            slipsHtml += `
            <div class="thermal-slip-item">
                <div class="slip-inner">
                    <div class="s-header">${data.headerText || 'HASHMI GRAINS'}</div>
                    <div class="s-name">${data.name}</div>
                    <div class="s-barcode">
                        <img src="${barcodeImg}" alt="barcode" />
                        <div class="s-code">${data.barcode}</div>
                    </div>
                    <div class="s-price">Rs ${data.price}${data.unit ? ' / ' + data.unit : ''}</div>
                    ${hasCustomText ? `<div class="s-custom">${data.customText}</div>` : ''}
                </div>
            </div>
            `;
        }

        const htmlContent = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Print Label - ${data.name}</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
            <style>
                * {
                    box-sizing: border-box !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    font-family: 'Poppins', 'Outfit', sans-serif;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
                @page {
                    size: 50mm 30mm;
                    margin: 0mm !important;
                }
                html, body {
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    width: 100% !important;
                }
                .thermal-slip-item {
                    width: 50mm !important;
                    height: 30mm !important;
                    max-height: 30mm !important;
                    box-sizing: border-box !important;
                    padding: 0.6mm 1mm !important;
                    margin: 0 auto !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                    page-break-after: always !important;
                    break-after: page !important;
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                    background: #ffffff !important;
                    overflow: hidden !important;
                }
                .thermal-slip-item:last-child {
                    page-break-after: auto !important;
                    break-after: auto !important;
                }
                .slip-inner {
                    width: 100%;
                    height: 100%;
                    box-sizing: border-box;
                    padding: 0.5mm 0.5mm;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: space-between;
                    overflow: hidden;
                }
                .s-header {
                    font-size: 6.5pt;
                    font-weight: 800;
                    color: #000000;
                    text-transform: uppercase;
                    letter-spacing: 0.03em;
                    line-height: 1;
                    max-width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                }
                .s-name {
                    font-size: 7.5pt;
                    font-weight: 800;
                    color: #000000;
                    text-transform: uppercase;
                    line-height: 1.05;
                    max-width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                    margin: 0.2mm 0;
                }
                .s-barcode {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }
                .s-barcode img {
                    height: 8.5mm;
                    max-width: 44mm;
                    object-fit: contain;
                    display: block;
                    margin: 0 auto;
                    image-rendering: pixelated;
                }
                .s-code {
                    font-family: 'Outfit', 'Courier New', monospace;
                    font-size: 6.8pt;
                    font-weight: 800;
                    color: #000000;
                    letter-spacing: 0.05em;
                    margin-top: 0.2mm;
                    line-height: 1;
                }
                .s-price {
                    font-family: 'Outfit', sans-serif;
                    font-size: 10pt;
                    font-weight: 900;
                    color: #000000;
                    line-height: 1;
                    margin-top: 0.2mm;
                }
                .s-custom {
                    font-size: 6.5pt;
                    font-weight: 800;
                    color: #000000;
                    margin-top: 0.2mm;
                    line-height: 1;
                    max-width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }
                @media print {
                    html, body {
                        margin: 0 !important;
                        padding: 0 !important;
                        background: #ffffff !important;
                    }
                    .thermal-slip-item {
                        width: 50mm !important;
                        height: 30mm !important;
                        max-height: 30mm !important;
                        page-break-after: always !important;
                        break-after: page !important;
                        page-break-inside: avoid !important;
                        break-inside: avoid !important;
                        display: flex !important;
                    }
                    .thermal-slip-item:last-child {
                        page-break-after: auto !important;
                        break-after: auto !important;
                    }
                }
            </style>
        </head>
        <body>
            ${slipsHtml}
            <script>
                function doAutoPrint() {
                    setTimeout(function() {
                        window.focus();
                        window.print();
                    }, 300);
                }
                if (document.readyState === 'complete') {
                    doAutoPrint();
                } else {
                    window.addEventListener('load', doAutoPrint);
                    setTimeout(doAutoPrint, 600);
                }
            <\/script>
        </body>
        </html>
        `;

        printWin.document.open();
        printWin.document.write(htmlContent);
        printWin.document.close();
    }

    function showLiveToast(msg) {
        const toast = document.getElementById('liveToastBox');
        const text = document.getElementById('liveToastMsg');
        if (toast && text) {
            text.textContent = msg;
            toast.style.display = 'inline-flex';
            toast.style.opacity = '1';
            clearTimeout(window.toastTimer);
            window.toastTimer = setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.style.display = 'none', 300);
            }, 3000);
        }
    }

    function flashRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            const originalBg = '#ffffff';
            row.querySelectorAll('td').forEach(td => {
                td.style.backgroundColor = '#dcfce7';
                td.style.transition = 'background-color 0.25s ease';
            });
            setTimeout(() => {
                row.querySelectorAll('td').forEach(td => {
                    td.style.backgroundColor = '';
                });
            }, 1200);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderInventoryBarcodes();

        // Attach AJAX handlers to Save Stock forms
        document.querySelectorAll('.ajax-stock-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const prodId = this.dataset.productId;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                try {
                    const formData = new FormData(this);
                    // Include alert_quantity if attached
                    const alertInput = document.getElementById('input-alert-' + prodId);
                    if (alertInput && !formData.has('alert_quantity')) {
                        formData.append('alert_quantity', alertInput.value);
                    }

                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Update current stock label
                        const stockLabel = document.getElementById('current-stock-label-' + prodId);
                        if (stockLabel) {
                            stockLabel.textContent = data.stock_quantity + ' units';
                            stockLabel.className = data.is_low ? 'stock-val-low' : 'stock-val-normal';
                        }
                        // Update stock input
                        const stockInput = document.getElementById('input-stock-' + prodId);
                        if (stockInput) {
                            stockInput.value = data.stock_quantity;
                        }
                        // Update alert input
                        if (alertInput && data.alert_quantity !== undefined) {
                            alertInput.value = data.alert_quantity;
                        }
                        flashRow('product-row-' + prodId);
                        showLiveToast(data.message || 'Stock updated successfully!');
                    } else {
                        // Fallback submit
                        this.submit();
                    }
                } catch(err) {
                    console.error(err);
                    this.submit();
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
        });

        // Attach AJAX handlers to Quick Adjust forms (+10 / -10)
        document.querySelectorAll('.ajax-adjust-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const prodId = this.dataset.productId;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;

                try {
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Update current stock label
                        const stockLabel = document.getElementById('current-stock-label-' + prodId);
                        if (stockLabel) {
                            stockLabel.textContent = data.stock_quantity + ' units';
                            stockLabel.className = data.is_low ? 'stock-val-low' : 'stock-val-normal';
                        }
                        // Update stock input
                        const stockInput = document.getElementById('input-stock-' + prodId);
                        if (stockInput) {
                            stockInput.value = data.stock_quantity;
                        }
                        flashRow('product-row-' + prodId);
                        showLiveToast(data.message || 'Stock adjusted successfully!');
                    } else {
                        this.submit();
                    }
                } catch(err) {
                    console.error(err);
                    this.submit();
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
        });
    });

    window.addEventListener('load', renderInventoryBarcodes);
    setTimeout(renderInventoryBarcodes, 200);
</script>
@endsection
