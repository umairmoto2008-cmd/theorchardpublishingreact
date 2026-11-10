@extends('layouts.app')

@section('title', 'Products Directory & Pricing - Hashmi Grains')
@section('header_title', 'Products Directory & Master Inventory')

@section('styles')
<style>
    /* Catalog Stats Summary Bar */
    .catalog-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .catalog-stat-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        box-shadow: var(--shadow-sm);
    }

    .catalog-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* Category Filter Pills */
    .category-pills-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        scrollbar-width: thin;
    }

    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.9rem;
        border-radius: 30px;
        font-size: 0.82rem;
        font-weight: 700;
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .cat-pill:hover {
        background: #f1f5f9;
        border-color: #0f5132;
        color: #0f5132;
    }

    .cat-pill.active {
        background: #0f5132;
        border-color: #0f5132;
        color: white;
        box-shadow: 0 2px 6px rgba(15, 81, 50, 0.3);
    }

    /* Action bar */
    .top-actions-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .search-filter-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: 1rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }

    /* Table Styling matching Image 1 Reference */
    .product-master-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }

    .product-master-table th {
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

    .product-master-table td {
        padding: 1.1rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .product-master-table tr:hover td {
        background: #f8fafc;
    }

    /* Product Name & SKU */
    .product-name-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 0.92rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
        line-height: 1.35;
    }

    .product-sku-sub {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        font-family: 'Outfit', sans-serif;
        margin-top: 4px;
    }

    /* Category */
    .product-category-text {
        font-size: 0.86rem;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    /* Price Styles */
    .price-buy {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        color: #475569;
        font-size: 0.92rem;
        white-space: nowrap;
    }

    .price-sale {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.96rem;
        white-space: nowrap;
    }

    /* Stock */
    .stock-num {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.92rem;
        white-space: nowrap;
    }
    .stock-num-normal { color: #0f172a; }
    .stock-num-low { color: #dc2626; }
    .stock-num-out { color: #dc2626; }

    /* Barcode Box */
    .barcode-col-td {
        width: 145px;
        min-width: 135px;
        max-width: 160px;
        text-align: center;
    }

    .table-barcode-svg {
        max-width: 135px !important;
        height: 38px !important;
        display: block !important;
        margin: 0 auto !important;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }
    .status-pill-in {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .status-pill-low {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }
    .status-pill-out {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Note Pill */
    .note-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 800;
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }

    /* Action buttons in Image 1 style */
    .action-btn-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 11px;
        height: 32px;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
        line-height: 1.2;
        white-space: nowrap;
    }

    /* Edit Button (Light Cyan/Blue) */
    .btn-action-edit {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }
    .btn-action-edit:hover {
        background: #e0f2fe;
        color: #0369a1;
        transform: translateY(-1px);
    }

    /* Label Button (Light Green/Mint) */
    .btn-action-label {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .btn-action-label:hover {
        background: #dcfce7;
        color: #15803d;
        transform: translateY(-1px);
    }

    /* Stock Button (Light Gray/Slate) */
    .btn-action-stock {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .btn-action-stock:hover {
        background: #f1f5f9;
        color: #1e293b;
        transform: translateY(-1px);
    }

    /* Delete Button (Light Red) */
    .btn-action-delete {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        width: 32px;
        height: 32px;
        padding: 0;
    }
    .btn-action-delete:hover {
        background: #fee2e2;
        color: #b91c1c;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showRestockModal: false,
    showImportModal: false,
    showLabelModal: false,
    previewBarcodeDataUrl: '',
    currentProduct: null,
    addForm: { name: '', barcode: '', expiry_date: '', category_id: '{{ $categories->first()->id ?? 1 }}', unit: 'Kg', purchase_price: 0, sale_price: 0, stock_quantity: 10, alert_quantity: 5, description: '' },
    editForm: { id: '', name: '', barcode: '', expiry_date: '', category_id: '', unit: 'Kg', purchase_price: 0, sale_price: 0, stock_quantity: 0, alert_quantity: 5, description: '' },
    restockForm: { id: '', name: '', unit: '', current_stock: 0, quantity: '', purchase_price: 0, notes: 'Restocked batch' },
    labelModal: { id: '', name: '', barcode: '', price: '0', unit: 'Kg', headerText: 'HASHMI GRAINS', customText: '', copies: 1, size: 'slip' },
    selectedProducts: [],
    
    toggleSelectAll() {
        if (this.selectedProducts.length === {{ $products->count() }}) {
            this.selectedProducts = [];
        } else {
            this.selectedProducts = @js($products->pluck('id'));
        }
    },
    
    calcMargin(cost, sale) {
        cost = parseFloat(cost) || 0;
        sale = parseFloat(sale) || 0;
        if (cost <= 0 || sale <= 0) return 0;
        return (((sale - cost) / cost) * 100).toFixed(1);
    },

    openEdit(id, name, barcode, expiry_date, category_id, unit, purchase_price, sale_price, stock_quantity, alert_quantity, description) {
        this.editForm = {
            id: id,
            name: name,
            barcode: barcode || '',
            expiry_date: expiry_date || '',
            category_id: category_id,
            unit: unit,
            purchase_price: purchase_price,
            sale_price: sale_price,
            stock_quantity: stock_quantity,
            alert_quantity: alert_quantity,
            description: description || ''
        };
        this.showEditModal = true;
    },

    openRestock(id, name, unit, current_stock, purchase_price) {
        this.restockForm = {
            id: id,
            name: name,
            unit: unit,
            current_stock: current_stock,
            quantity: '',
            purchase_price: purchase_price,
            notes: 'Restocked inventory'
        };
        this.showRestockModal = true;
    },

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

    <!-- Row 1: High Level Catalog Stats -->
    <div class="catalog-stats-grid">
        <div class="catalog-stat-card">
            <div class="catalog-stat-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Active Catalog</div>
                <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: var(--gray-900);">
                    {{ $totalProductsCount }} Items
                </div>
            </div>
        </div>

        <div class="catalog-stat-card">
            <div class="catalog-stat-icon" style="background: #f0fdfa; color: #0d9488;">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Stock Cost Value</div>
                <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: #0d9488;">
                    Rs. {{ number_format($totalValuationCost, 0) }}
                </div>
            </div>
        </div>

        <div class="catalog-stat-card">
            <div class="catalog-stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-shop"></i>
            </div>
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Retail Valuation</div>
                <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: #2563eb;">
                    Rs. {{ number_format($totalValuationSale, 0) }}
                </div>
            </div>
        </div>

        <a href="{{ route('products.index', ['stock_filter' => 'low']) }}" class="catalog-stat-card" style="text-decoration: none; border-color: {{ $lowStockCount > 0 ? '#fecaca' : 'var(--gray-200)' }};">
            <div class="catalog-stat-icon" style="background: {{ $lowStockCount > 0 ? '#fff1f2' : '#f8fafc' }}; color: {{ $lowStockCount > 0 ? '#dc2626' : '#64748b' }};">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Low Stock Warning</div>
                <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 800; color: {{ $lowStockCount > 0 ? '#dc2626' : '#16a34a' }};">
                    {{ $lowStockCount }} Items
                </div>
            </div>
        </a>
    </div>

    <!-- Row 2: Top Action Toolbar -->
    <div class="top-actions-wrapper">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button @click="showAddModal = true" class="btn btn-primary">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Add New Product</span>
            </button>
            <a href="{{ route('products.bulk_add') }}" class="btn btn-outline" style="background: white;">
                <i class="fa-solid fa-bolt" style="color: #16a34a;"></i>
                <span>Bulk Add Products</span>
            </a>
            <button @click="showImportModal = true" class="btn btn-outline" style="background: white;">
                <i class="fa-solid fa-file-import" style="color: #0284c7;"></i>
                <span>Import Excel/CSV</span>
            </button>
            <a href="{{ route('products.export_csv') }}" class="btn btn-outline" style="background: white;">
                <i class="fa-solid fa-file-export" style="color: #d97706;"></i>
                <span>Export CSV</span>
            </a>
        </div>

        <div style="font-size: 0.85rem; font-weight: 700; color: var(--gray-600);">
            Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} Products
        </div>
    </div>

    <!-- Row 3: 1-Click Category Filter Pills -->
    <div class="category-pills-bar">
        <a href="{{ route('products.index', array_merge(request()->except(['category_id', 'page']))) }}" 
           class="cat-pill {{ empty($categoryId) ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>All Categories ({{ $totalProductsCount }})</span>
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('products.index', array_merge(request()->except(['category_id', 'page']), ['category_id' => $cat->id])) }}" 
               class="cat-pill {{ $categoryId == $cat->id ? 'active' : '' }}">
                <i class="{{ $cat->icon ?: 'fa-solid fa-box' }}"></i>
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>

    <!-- Row 4: Search & Sorting Filter Bar -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('products.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; align-items: flex-end;">
            @if($categoryId)
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
            @endif

            <!-- Search -->
            <div>
                <label class="form-label">Search Product / Barcode</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: var(--gray-400);"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, barcode..." class="form-control" style="padding-left: 2.25rem;">
                </div>
            </div>

            <!-- Stock Filter -->
            <div>
                <label class="form-label">Stock Status</label>
                <select name="stock_filter" class="form-control">
                    <option value="">All Stock Levels</option>
                    <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>Low Stock Warnings</option>
                    <option value="out" {{ $stockFilter === 'out' ? 'selected' : '' }}>Out of Stock Only</option>
                </select>
            </div>

            <!-- Sort Option -->
            <div>
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-control">
                    <option value="name_asc" {{ ($sort ?? 'name_asc') === 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    <option value="name_desc" {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                    <option value="price_low" {{ ($sort ?? '') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ ($sort ?? '') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="stock_low" {{ ($sort ?? '') === 'stock_low' ? 'selected' : '' }}>Stock: Low to High</option>
                    <option value="stock_high" {{ ($sort ?? '') === 'stock_high' ? 'selected' : '' }}>Stock: High to Low</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 0.4rem;">
                <button type="submit" class="btn btn-primary" style="height: 40px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if($search || $categoryId || $stockFilter || ($sort && $sort !== 'name_asc'))
                    <a href="{{ route('products.index') }}" class="btn btn-outline" style="height: 40px;" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Row 5: Clean Master Product Table (Matching Image 1 Design) -->
    <div class="card" style="border: 1px solid var(--gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); background: white;">
        <div class="table-responsive">
            <table class="product-master-table">
                <thead>
                    <tr>
                        <th style="width: 44px; text-align: center;">
                            <input type="checkbox" @click="toggleSelectAll()" :checked="selectedProducts.length === {{ $products->count() }} && {{ $products->count() }} > 0" style="width: 16px; height: 16px; accent-color: #0f5132; cursor: pointer;">
                        </th>
                        <th style="min-width: 210px;">PRODUCT NAME</th>
                        <th style="width: 130px;">CATEGORY</th>
                        <th style="width: 105px;">BUY PRICE</th>
                        <th style="width: 110px;">SALE PRICE</th>
                        <th style="width: 100px;">STOCK</th>
                        <th style="width: 145px; text-align: center;">BARCODE</th>
                        <th style="width: 115px; text-align: center;">STATUS</th>
                        <th style="width: 140px; text-align: center;">NOTE</th>
                        <th style="min-width: 250px; text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <!-- Checkbox -->
                            <td style="text-align: center; width: 44px;">
                                <input type="checkbox" :value="{{ $p->id }}" x-model="selectedProducts" style="width: 16px; height: 16px; accent-color: #0f5132; cursor: pointer;">
                            </td>

                            <!-- Product Name -->
                            <td>
                                <div class="product-name-title">{{ $p->name }}</div>
                                <div class="product-sku-sub">SKU: {{ $p->barcode }}</div>
                            </td>

                            <!-- Category -->
                            <td style="width: 130px;">
                                <div class="product-category-text">{{ $p->category->name ?? 'General' }}</div>
                            </td>

                            <!-- Buy Price -->
                            <td style="width: 105px;">
                                <div class="price-buy">Rs {{ number_format($p->purchase_price, 0) }}</div>
                            </td>

                            <!-- Sale Price -->
                            <td style="width: 110px;">
                                <div class="price-sale">Rs {{ number_format($p->sale_price, 0) }}</div>
                            </td>

                            <!-- Stock -->
                            <td style="width: 100px;">
                                @if($p->stock_quantity <= 0)
                                    <strong class="stock-num stock-num-out">0 units</strong>
                                @elseif($p->isLowStock())
                                    <strong class="stock-num stock-num-low">{{ (float)$p->stock_quantity }} units</strong>
                                @else
                                    <strong class="stock-num stock-num-normal">{{ (float)$p->stock_quantity }} units</strong>
                                @endif
                            </td>

                            <!-- Barcode with Visual Bars & Text -->
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

                            <!-- Status Pill -->
                            <td style="text-align: center; width: 115px;">
                                @if($p->stock_quantity <= 0)
                                    <span class="status-pill status-pill-out">Out of Stock</span>
                                @elseif($p->isLowStock())
                                    <span class="status-pill status-pill-low">Low Stock</span>
                                @else
                                    <span class="status-pill status-pill-in">In Stock</span>
                                @endif
                            </td>

                            <!-- Note Pill -->
                            <td style="text-align: center; width: 140px;">
                                <span class="note-pill">
                                    <i class="fa-solid fa-folder"></i>
                                    <span>{{ strtoupper($p->category->name ?? 'SHOP') }}</span>
                                </span>
                            </td>

                            <!-- Actions Matching Image 1 -->
                            <td style="text-align: right; min-width: 250px;">
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem; justify-content: flex-end;">
                                    <!-- Edit Button (Light Cyan/Blue) -->
                                    <button 
                                        type="button"
                                        @click.stop="openEdit({{ $p->id }}, @js($p->name), @js($p->barcode ?? ''), @js($p->expiry_date ? $p->expiry_date->format('Y-m-d') : ''), {{ $p->category_id }}, @js($p->unit), {{ (float)$p->purchase_price }}, {{ (float)$p->sale_price }}, {{ (float)$p->stock_quantity }}, {{ (float)$p->alert_quantity }}, @js($p->description ?? ''))" 
                                        class="action-btn-custom btn-action-edit"
                                        title="Edit Product Details"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>

                                    <!-- Label Button (Light Green - Opens Print Modal) -->
                                    <button 
                                        type="button"
                                        @click.stop="openPrintModal({{ $p->id }}, @js($p->name), @js($p->barcode ?? ''), {{ (float)$p->sale_price }}, @js($p->unit), @js($p->expiry_date ? $p->expiry_date->format('d/m/Y') : ''), @js($p->category->name ?? 'Hashmi Grains'))" 
                                        class="action-btn-custom btn-action-label"
                                        title="Print Barcode Label for {{ $p->name }}"
                                    >
                                        <i class="fa-solid fa-barcode"></i> Label
                                    </button>

                                    <!-- Stock Button (Light Gray Dropdown) -->
                                    <button 
                                        type="button"
                                        @click.stop="openRestock({{ $p->id }}, @js($p->name), @js($p->unit), {{ (float)$p->stock_quantity }}, {{ (float)$p->purchase_price }})" 
                                        class="action-btn-custom btn-action-stock"
                                        title="Adjust / Restock Stock"
                                    >
                                        Stock <i class="fa-solid fa-caret-down" style="font-size: 0.65rem;"></i>
                                    </button>

                                    <!-- Delete Button (Light Red) -->
                                    <form action="{{ route('products.destroy', $p) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete \'{{ $p->name }}\'?');" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn-custom btn-action-delete" title="Delete Product">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-box-open" style="font-size: 2.5rem; margin-bottom: 0.5rem; color: var(--gray-300);"></i>
                                <p style="font-weight: 700; font-size: 1rem; color: var(--gray-600);">No products found matching your filter</p>
                                <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm" style="margin-top: 0.5rem;">Reset Filters</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Clean Pagination Footer -->
        @if($products->hasPages())
            <div style="padding: 1.25rem; border-top: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.85rem; color: var(--gray-500);">
                    Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> products
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal: Add New Product -->
    <div class="modal-overlay" x-show="showAddModal" style="display: none;" @click.self="showAddModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header" style="background: #ecfdf5;">
                <h3 class="card-title">
                    <i class="fa-solid fa-circle-plus" style="color: #059669;"></i>
                    <span>Add New Product to Catalog</span>
                </h3>
                <button type="button" @click="showAddModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--gray-500);">&times;</button>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Product Name (English & Urdu) *</label>
                        <input type="text" name="name" x-model="addForm.name" class="form-control" placeholder="e.g. Pure Desi Chakki Atta (5kg)" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="category_id" x-model="addForm.category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Packaging Unit *</label>
                            <select name="unit" x-model="addForm.unit" class="form-control" required>
                                <option value="1kg">1kg</option>
                                <option value="5kg">5kg</option>
                                <option value="10kg">10kg</option>
                                <option value="Kg">Kg (Loose)</option>
                                <option value="500g">500g</option>
                                <option value="800g">800g</option>
                                <option value="250ml">250ml</option>
                                <option value="500ml">500ml</option>
                                <option value="700ml">700ml</option>
                                <option value="900ml">900ml</option>
                                <option value="1 Litre">1 Litre</option>
                                <option value="Bottle">Bottle</option>
                                <option value="Jar">Jar</option>
                                <option value="Pack">Pack</option>
                                <option value="Piece">Piece</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Purchase / Cost Price (Rs) *</label>
                            <input type="number" step="any" min="0" name="purchase_price" x-model="addForm.purchase_price" class="form-control" placeholder="e.g. 450" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sale / Retail Price (Rs) *</label>
                            <input type="number" step="any" min="0" name="sale_price" x-model="addForm.sale_price" class="form-control" placeholder="e.g. 600" required>
                        </div>
                    </div>

                    <!-- Live Margin Preview Banner -->
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--gray-600);">Calculated Profit Margin:</span>
                        <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.1rem; color: #059669;" x-text="calcMargin(addForm.purchase_price, addForm.sale_price) + '%'">
                            0%
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Initial Stock Quantity *</label>
                            <input type="number" step="any" min="0" name="stock_quantity" x-model="addForm.stock_quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Low Stock Alert Level</label>
                            <input type="number" step="any" min="1" name="alert_quantity" x-model="addForm.alert_quantity" class="form-control" placeholder="Default: 5">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Barcode / SKU (Optional)</label>
                            <input type="text" name="barcode" x-model="addForm.barcode" class="form-control" placeholder="Auto generated if empty">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Expiry Date (Optional)</label>
                            <input type="date" name="expiry_date" x-model="addForm.expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showAddModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Product -->
    <div class="modal-overlay" x-show="showEditModal" style="display: none;" @click.self="showEditModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-pen-to-square" style="color: #2563eb;"></i>
                    <span>Edit Product Details</span>
                </h3>
                <button type="button" @click="showEditModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--gray-500);">&times;</button>
            </div>
            <form action="{{ route('products.update.direct') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" :value="editForm.id" x-model="editForm.id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" x-model="editForm.name" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="category_id" x-model="editForm.category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unit *</label>
                            <input type="text" name="unit" x-model="editForm.unit" class="form-control" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Purchase Price (Rs) *</label>
                            <input type="number" step="any" min="0" name="purchase_price" x-model="editForm.purchase_price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sale Price (Rs) *</label>
                            <input type="number" step="any" min="0" name="sale_price" x-model="editForm.sale_price" class="form-control" required>
                        </div>
                    </div>

                    <!-- Live Margin Preview Banner -->
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--gray-600);">Calculated Margin:</span>
                        <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.1rem; color: #059669;" x-text="calcMargin(editForm.purchase_price, editForm.sale_price) + '%'">
                            0%
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Available Stock *</label>
                            <input type="number" step="any" min="0" name="stock_quantity" x-model="editForm.stock_quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Low Stock Alert</label>
                            <input type="number" step="any" min="1" name="alert_quantity" x-model="editForm.alert_quantity" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Barcode / SKU</label>
                            <input type="text" name="barcode" x-model="editForm.barcode" class="form-control" placeholder="e.g. HG-RICE-1KG">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Expiry Date (تاریخ میعاد)</label>
                            <input type="date" name="expiry_date" x-model="editForm.expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showEditModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Quick Restock -->
    <div class="modal-overlay" x-show="showRestockModal" style="display: none;" @click.self="showRestockModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header" style="background: #ecfdf5;">
                <h3 class="card-title">
                    <i class="fa-solid fa-boxes-packing" style="color: #059669;"></i>
                    <span>Quick Restock Inventory</span>
                </h3>
                <button type="button" @click="showRestockModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--gray-500);">&times;</button>
            </div>
            <form action="{{ route('products.restock.direct') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" :value="restockForm.id" x-model="restockForm.id">
                <div class="modal-body">
                    <div style="background: #f8fafc; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Selected Item:</div>
                        <div style="font-weight: 800; font-size: 1.05rem; color: var(--gray-900);" x-text="restockForm.name"></div>
                        <div style="font-size: 0.82rem; color: var(--gray-600); margin-top: 4px;">
                            Current In-Stock: <strong style="color: #059669;" x-text="restockForm.current_stock + ' ' + restockForm.unit"></strong>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Quantity to Add *</label>
                            <input type="number" step="any" min="0.01" name="quantity" x-model="restockForm.quantity" class="form-control" placeholder="e.g. 50" required autofocus>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Purchase / Buying Price (Rs)</label>
                            <input type="number" step="any" min="0" name="purchase_price" x-model="restockForm.purchase_price" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Restock Reference / Supplier Note</label>
                        <input type="text" name="notes" x-model="restockForm.notes" class="form-control" placeholder="e.g. Fresh wholesale stock arrival">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showRestockModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm & Restock Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Import CSV -->
    <div class="modal-overlay" x-show="showImportModal" style="display: none;" @click.self="showImportModal = false">
        <div class="modal-box" @click.stop>
            <div class="modal-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-file-csv" style="color: #0284c7;"></i>
                    <span>Import Products from CSV / Excel</span>
                </h3>
                <button type="button" @click="showImportModal = false" style="background:none;border:none;font-size:1.3rem;cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('products.import_csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p style="font-size: 0.85rem; color: var(--gray-600); margin-bottom: 1rem;">
                        Upload a CSV file with columns: <code>Product Name, Barcode, Category, Unit, Purchase Price, Sale Price, Stock Quantity, Alert Quantity</code>.
                    </p>

                    <div class="form-group">
                        <label class="form-label">Select CSV File *</label>
                        <input type="file" name="csv_file" accept=".csv,.txt" class="form-control" required>
                    </div>

                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="{{ route('products.export_csv') }}" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-download"></i> Download Sample CSV Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showImportModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Import</button>
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
    function renderBarcodes() {
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
                /* Do not break after last sticker to avoid feeding empty label */
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

    document.addEventListener('DOMContentLoaded', renderBarcodes);
    window.addEventListener('load', renderBarcodes);
    setTimeout(renderBarcodes, 150);
</script>
@endsection
