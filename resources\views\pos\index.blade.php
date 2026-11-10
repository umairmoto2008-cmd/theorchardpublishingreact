@extends('layouts.app')

@section('title', 'POS Terminal - Hashmi Grains')
@section('header_title', 'Billing & POS Terminal')

@section('styles')
<style>
    .pos-wrapper {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 1.25rem;
        height: calc(100vh - var(--header-height) - 3rem);
        min-height: 580px;
    }

    /* Left Side: Catalog & Search */
    .pos-catalog {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        min-width: 0;
        height: 100%;
        overflow: hidden;
    }

    .pos-search-bar {
        background: white;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: var(--shadow-sm);
    }

    .pos-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--gray-900);
    }

    .category-pills {
        display: flex;
        gap: 0.45rem;
        overflow-x: auto;
        padding-bottom: 0.4rem;
        scrollbar-width: thin;
    }

    .category-pill {
        white-space: nowrap;
        padding: 0.45rem 0.85rem;
        border-radius: 9999px;
        background: white;
        border: 1px solid var(--gray-200);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--gray-700);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }

    .category-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .category-pill.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 10px rgba(15, 81, 50, 0.25);
    }

    /* Clean, Spacious Product Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(225px, 1fr));
        grid-auto-rows: min-content;
        align-content: start;
        gap: 0.9rem;
        overflow-y: auto;
        padding: 0.2rem 0.35rem 1rem 0.1rem;
        flex: 1;
        min-height: 0;
    }

    .product-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 0.95rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        user-select: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        min-height: 135px;
    }

    .product-card:hover {
        transform: translateY(-2px);
        border-color: #10b981;
        box-shadow: 0 8px 18px rgba(15, 81, 50, 0.1);
    }

    .product-card:active {
        transform: scale(0.98);
    }

    .card-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.45rem;
    }

    .prod-badge-unit {
        font-size: 0.68rem;
        font-weight: 800;
        background: #f1f5f9;
        color: #475569;
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .prod-stock-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        letter-spacing: 0.02em;
    }
    .stock-ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .stock-low { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    .prod-title-group {
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 3px;
    }

    /* Clean Urdu text directly below product title */
    .product-name-ur {
        font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, sans-serif;
        font-size: 0.82rem;
        color: #047857;
        font-weight: 600;
        line-height: 1.35;
    }

    .product-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 0.55rem;
        border-top: 1px dashed #e2e8f0;
        gap: 0.5rem;
    }

    .price-stack {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .product-price {
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f5132;
        line-height: 1.15;
    }

    .product-cost-row {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        background: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 5px;
        border: 1px solid #fde68a;
        width: fit-content;
        margin-top: 2px;
    }

    .cost-lbl {
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #b45309;
    }

    .cost-val {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        color: #78350f;
    }

    .eye-price-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1.5px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        white-space: nowrap;
        user-select: none;
    }

    .eye-price-toggle-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .eye-price-toggle-btn.active {
        background: #ecfdf5;
        border-color: #10b981;
        color: #065f46;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
    }

    .eye-price-toggle-btn.active i {
        color: #059669;
    }

    .add-icon-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #ecfdf5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: bold;
        transition: all 0.15s ease;
        flex-shrink: 0;
        border: 1px solid #a7f3d0;
    }

    .product-card:hover .add-icon-btn {
        background: #10b981;
        color: white;
        border-color: #10b981;
        transform: scale(1.05);
    }

    /* Right Side: Cart */
    .pos-cart-panel {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-md);
        height: 100%;
        overflow: hidden;
    }

    .cart-header {
        padding: 0.85rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .cart-header h2 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .customer-inputs {
        padding: 0.65rem 1rem;
        background: #fafafa;
        border-bottom: 1px solid var(--gray-200);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .cart-items-container {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .cart-item-row {
        display: grid;
        grid-template-columns: 1fr auto auto auto;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.5rem;
        border-bottom: 1px solid var(--gray-100);
        transition: background 0.15s;
    }

    .cart-item-row:hover {
        background: #f8fafc;
    }

    .cart-item-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1.2;
    }

    .cart-item-price-info {
        font-size: 0.72rem;
        color: var(--gray-500);
    }

    .qty-control {
        display: flex;
        align-items: center;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: white;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        background: #f1f5f9;
        border: none;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-700);
    }
    .qty-btn:hover { background: #e2e8f0; }

    .qty-input {
        width: 38px;
        height: 24px;
        border: none;
        text-align: center;
        font-size: 0.82rem;
        font-weight: 700;
        outline: none;
    }

    .cart-item-total {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--gray-900);
        text-align: right;
        min-width: 60px;
    }

    .remove-btn {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        font-size: 0.85rem;
    }

    .cart-footer {
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-top: 1px solid var(--gray-200);
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        font-size: 0.82rem;
        color: var(--gray-600);
        margin-bottom: 0.3rem;
    }

    .grand-total-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
        border-top: 1.5px dashed var(--gray-300);
        margin-top: 0.25rem;
    }

    .grand-total-label {
        font-family: 'Outfit', sans-serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--gray-900);
    }

    .grand-total-val {
        font-family: 'Outfit', sans-serif;
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f5132;
    }

    .quick-cash-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.35rem;
        margin: 0.5rem 0;
    }

    .cash-pill {
        padding: 0.3rem 0;
        background: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--gray-700);
        cursor: pointer;
        text-align: center;
    }
    .cash-pill:hover { background: #e2e8f0; }

    .tender-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 0.6rem;
    }

    .btn-checkout {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(135deg, #15803d, #16a34a);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-size: 1rem;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        transition: all 0.2s;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(22, 163, 74, 0.45);
    }

    .btn-checkout:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Receipt & WhatsApp Modal */
    .receipt-modal-preview {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.85rem;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.82rem;
        max-height: 240px;
        overflow-y: auto;
        white-space: pre-wrap;
        line-height: 1.35;
        background: #fafafa;
    }

    .whatsapp-btn {
        background: #25D366;
        color: white;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: var(--radius-md);
        box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
    }
    .whatsapp-btn:hover {
        background: #1eb956;
    }

    @media (max-width: 992px) {
        .pos-wrapper {
            grid-template-columns: 1fr;
            height: auto;
        }
        .pos-cart-panel {
            min-height: 450px;
        }
    }
</style>
@endsection

@section('content')
<div x-data="posApp()" x-init="initPos()" class="pos-wrapper">
    <!-- Left: Catalog & Search -->
    <div class="pos-catalog">
        <!-- Search Bar -->
        <div class="pos-search-bar">
            <i class="fa-solid fa-barcode" style="font-size: 1.3rem; color: #16a34a;"></i>
            <input 
                type="text" 
                x-ref="searchInput"
                x-model="searchQuery" 
                @input.debounce.200ms="filterProducts()" 
                @keydown.enter="handleBarcodeEnter()"
                placeholder="Scan barcode or search by English or Urdu name..." 
                class="pos-search-input"
                autofocus
            >
            <button x-show="searchQuery" @click="searchQuery = ''; filterProducts()" class="btn btn-outline btn-sm" style="border:none;">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Eye Option to Show/Hide Retailer & Cost Price -->
            <button 
                type="button" 
                @click="togglePriceView()" 
                class="eye-price-toggle-btn"
                :class="{ 'active': showCostPrice }"
                title="Show/Hide Cost Rates"
            >
                <i :class="showCostPrice ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'"></i>
                <span x-text="showCostPrice ? 'Cost Price: ON' : 'Cost Price: OFF'"></span>
            </button>
        </div>

        <!-- Category Pills -->
        <div class="category-pills">
            <button 
                class="category-pill" 
                :class="{ 'active': activeCategory === null }" 
                @click="setCategory(null)"
            >
                <i class="fa-solid fa-layer-group"></i>
                <span>All Products</span>
                <span style="opacity: 0.7;">({{ count($products) }})</span>
            </button>
            @foreach($categories as $cat)
                <button 
                    class="category-pill" 
                    :class="{ 'active': activeCategory === {{ $cat->id }} }" 
                    @click="setCategory({{ $cat->id }})"
                >
                    <i class="{{ $cat->icon }}"></i>
                    <span>{{ $cat->name }}</span>
                    <span style="opacity: 0.7;">({{ $cat->products_count }})</span>
                </button>
            @endforeach
        </div>

        <!-- Products Grid (Clean, Spacious Cards) -->
        <div class="products-grid">
            <template x-for="p in filteredProducts" :key="p.id">
                <div class="product-card" @click="addToCart(p)">
                    <div>
                        <div class="card-top-row">
                            <span class="prod-badge-unit" x-text="p.unit"></span>
                            <span 
                                class="prod-stock-badge" 
                                :class="p.stock_quantity <= p.alert_quantity ? 'stock-low' : 'stock-ok'"
                                x-text="'Stock: ' + Math.round(p.stock_quantity)"
                            ></span>
                        </div>
                        <div class="prod-title-group">
                            <div class="product-name" x-text="p.name"></div>
                            <!-- Urdu Name Directly Under Product -->
                            <div class="product-name-ur" x-text="getUrduName(p.name)"></div>
                        </div>
                    </div>
                    <div class="product-footer">
                        <div class="price-stack">
                            <!-- Customer Sale Price -->
                            <div class="product-price">
                                Rs. <span x-text="formatNumber(p.sale_price)"></span>
                            </div>
                            <!-- Retailer / Purchase Cost Price -->
                            <div x-show="showCostPrice" class="product-cost-row">
                                <span class="cost-lbl">Cost:</span>
                                <span class="cost-val" x-text="'Rs. ' + formatNumber(p.purchase_price)"></span>
                            </div>
                        </div>
                        <div class="add-icon-btn">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="filteredProducts.length === 0" style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--gray-400);">
                <i class="fa-solid fa-box-open" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                <p style="font-weight: 600;">No matching products found</p>
                <p style="font-size: 0.8rem;">Try a different keyword in English or Urdu</p>
            </div>
        </div>
    </div>

    <!-- Right: Sticky Cart Panel -->
    <div class="pos-cart-panel">
        <div class="cart-header">
            <h2>
                <i class="fa-solid fa-cart-shopping" style="color: #16a34a;"></i>
                <span>Current Bill</span>
            </h2>
            <button @click="clearCart()" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: #fca5a5;" :disabled="cart.length === 0">
                <i class="fa-solid fa-trash-can"></i> Clear
            </button>
        </div>

        <!-- Customer Inputs -->
        <div class="customer-inputs">
            <div>
                <input type="text" x-model="customerName" placeholder="Customer Name (Optional)" class="form-control" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">
            </div>
            <div>
                <input type="text" x-model="customerPhone" placeholder="WhatsApp (0300...)" class="form-control" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;">
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="cart-items-container">
            <template x-if="cart.length === 0">
                <div style="text-align: center; padding: 3rem 1rem; color: var(--gray-400);">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #cbd5e1;"></i>
                    <p style="font-weight: 600; font-size: 0.9rem;">Cart is Empty</p>
                    <p style="font-size: 0.75rem;">Scan barcode or click items to add</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="cart-item-row">
                    <div>
                        <div class="cart-item-title" x-text="item.name"></div>
                        <div class="cart-item-price-info">
                            Rs. <span x-text="formatNumber(item.price)"></span> / <span x-text="item.unit"></span>
                        </div>
                    </div>

                    <div class="qty-control">
                        <button class="qty-btn" @click="decreaseQty(index)">-</button>
                        <input type="number" step="any" min="0.01" class="qty-input" x-model.number="item.quantity" @input="updateLineTotal(index)">
                        <button class="qty-btn" @click="increaseQty(index)">+</button>
                    </div>

                    <div class="cart-item-total">
                        Rs. <span x-text="formatNumber(item.subtotal)"></span>
                    </div>

                    <button class="remove-btn" @click="removeFromCart(index)">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </template>
        </div>

        <!-- Cart Footer & Calculations -->
        <div class="cart-footer">
            <div class="summary-line">
                <span>Subtotal Items (<span x-text="cart.length"></span>):</span>
                <span style="font-weight: 700;">Rs. <span x-text="formatNumber(calculateSubtotal())"></span></span>
            </div>

            <div class="summary-line" style="align-items: center;">
                <span>Discount (Rs):</span>
                <input type="number" min="0" x-model.number="discount" class="form-control" style="width: 80px; padding: 0.2rem 0.4rem; font-size: 0.82rem; text-align: right;">
            </div>

            <div class="grand-total-line">
                <span class="grand-total-label">Grand Total:</span>
                <span class="grand-total-val">Rs. <span x-text="formatNumber(calculateGrandTotal())"></span></span>
            </div>

            <!-- Quick Cash Buttons -->
            <div class="quick-cash-row">
                <button class="cash-pill" @click="cashGiven = calculateGrandTotal()">Exact</button>
                <button class="cash-pill" @click="cashGiven = 500">Rs. 500</button>
                <button class="cash-pill" @click="cashGiven = 1000">Rs. 1,000</button>
                <button class="cash-pill" @click="cashGiven = 5000">Rs. 5,000</button>
            </div>

            <!-- Tender & Payment Method -->
            <div class="tender-row">
                <div>
                    <label class="form-label" style="font-size: 0.72rem; margin-bottom: 2px;">Payment Method</label>
                    <select x-model="paymentMethod" class="form-control" style="font-size: 0.8rem; padding: 0.35rem 0.5rem;">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card / POS</option>
                        <option value="easypaisa">📱 EasyPaisa / JazzCash</option>
                        <option value="online">🏦 Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.72rem; margin-bottom: 2px;">Cash Received (Rs)</label>
                    <input type="number" min="0" x-model.number="cashGiven" class="form-control" style="font-size: 0.85rem; padding: 0.35rem 0.5rem; font-weight: 700;">
                </div>
            </div>

            <div class="summary-line" style="font-size: 0.95rem; font-weight: 700; color: #15803d; margin-bottom: 0.75rem;">
                <span>Change / Baqi:</span>
                <span>Rs. <span x-text="formatNumber(calculateChange())"></span></span>
            </div>

            <button 
                class="btn-checkout" 
                :disabled="cart.length === 0 || isProcessing" 
                @click="processCheckout()"
            >
                <i class="fa-solid fa-circle-check"></i>
                <span x-text="isProcessing ? 'Processing...' : 'Complete & Print Receipt'"></span>
            </button>
        </div>
    </div>

    <!-- Sale Completed Success Modal -->
    <div class="modal-overlay" x-show="showSuccessModal" style="display: none;">
        <div class="modal-box" @click.away="closeSuccessModal()">
            <div class="modal-header" style="background: #ecfdf5; border-bottom-color: #a7f3d0;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #16a34a; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #065f46;">Sale Completed!</h3>
                        <p style="font-size: 0.78rem; color: #047857;" x-text="'Invoice: ' + (lastSale?.invoice_number || '')"></p>
                    </div>
                </div>
                <button @click="closeSuccessModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#065f46;">&times;</button>
            </div>

            <div class="modal-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--gray-500);">TOTAL BILLED</div>
                        <div style="font-family: 'Outfit'; font-size: 1.4rem; font-weight: 800; color: #0f5132;" x-text="'Rs. ' + formatNumber(lastSale?.grand_total || 0)"></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: var(--gray-500);">CHANGE RETURNED</div>
                        <div style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 700; color: #d97706;" x-text="'Rs. ' + formatNumber(lastSale?.change_amount || 0)"></div>
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="font-size: 0.78rem;">Invoice Text Preview:</label>
                    <div class="receipt-modal-preview" x-text="whatsappText"></div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Print Thermal Receipt Button (Same Tab Direct Print) -->
                    <button 
                        type="button"
                        @click="printReceiptDirectly(lastSale?.id)" 
                        class="btn btn-primary btn-lg" 
                        style="width: 100%; cursor: pointer; border: none;"
                    >
                        <i class="fa-solid fa-print"></i>
                        <span>Print Thermal Receipt (58mm/80mm)</span>
                    </button>

                    <!-- WhatsApp Share Button -->
                    <a 
                        :href="whatsappUrl" 
                        target="_blank" 
                        class="whatsapp-btn"
                        style="width: 100%; font-size: 1rem;"
                    >
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.3rem;"></i>
                        <span>Send Invoice via WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="modal-footer" style="justify-content: center;">
                <button class="btn btn-outline" @click="closeSuccessModal()" style="font-weight: 700; width: 100%;">
                    <i class="fa-solid fa-rotate-left" style="margin-right: 6px;"></i>
                    <span>Start New Sale</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function posApp() {
    return {
        allProducts: @json($products),
        filteredProducts: [],
        searchQuery: '',
        activeCategory: null,
        cart: [],
        customerName: '',
        customerPhone: '',
        discount: 0,
        paymentMethod: 'cash',
        cashGiven: 0,
        isProcessing: false,
        showSuccessModal: false,
        lastSale: null,
        whatsappUrl: '',
        whatsappText: '',
        showCostPrice: localStorage.getItem('pos_show_cost') !== null ? (localStorage.getItem('pos_show_cost') === 'true') : true,

        urduProductNames: {
            'Super Kernel Basmati Rice (1kg)': 'سپر کرنل باسمتی چاول (1 کلو)',
            'Super Kernel Basmati Rice (5kg)': 'سپر کرنل باسمتی چاول (5 کلو)',
            'Super Kernel Basmati Rice (10kg)': 'سپر کرنل باسمتی چاول (10 کلو)',
            'Kainat Steam Rice (1kg)': 'کائنات سٹیم چاول (1 کلو)',
            'Chakki Atta (5kg)': 'خالص چکی آٹا (5 کلو)',
            'Coconut Oil (60ml)': 'کھوپرے کا تیل (60 ملی)',
            'Almond Oil (50ml)': 'روغن بادام (50 ملی)',
            'Apricot Oil (50ml)': 'خوبانی کا تیل (50 ملی)',
            'Rosemary Infused Sesame Oil (50ml)': 'روزمری تل کا تیل (50 ملی)',
            'Pumpkin Seed Oil (50ml)': 'کدو کے بیج کا تیل (50 ملی)',
            'Walnut Oil (50ml)': 'روغن اخروٹ (50 ملی)',
            'Coconut Oil (100ml)': 'کھوپرے کا تیل (100 ملی)',
            'Sunflower Seed Oil (100ml)': 'سورج مکھی تیل (100 ملی)',
            'Sesame Seed Oil (100ml)': 'تل کا تیل (100 ملی)',
            'Kalonji Oil (100ml)': 'کلونجی کا تیل (100 ملی)',
            'Castor Oil (100ml)': 'ارنڈی کا تیل / کیسٹر آئل (100 ملی)',
            'Rosemary Infused Sesame Oil (100ml)': 'روزمری تل کا تیل (100 ملی)',
            'Coconut Oil (250ml)': 'کھوپرے کا تیل (250 ملی)',
            'Sunflower Seed Oil (250ml)': 'سورج مکھی تیل (250 ملی)',
            'Mustard Oil (250ml)': 'سرسوں کا تیل (250 ملی)',
            'Extra Virgin Olive Oil (250ml)': 'زیتون کا تیل (250 ملی)',
            'Coconut Oil (500ml)': 'کھوپرے کا تیل (500 ملی)',
            'Sunflower Seed Oil (500ml)': 'سورج مکھی تیل (500 ملی)',
            'Mustard Oil (500ml)': 'سرسوں کا تیل (500 ملی)',
            'Coconut Oil (1 Liter)': 'کھوپرے کا تیل (1 لیٹر)',
            'Sunflower Seed Oil (1 Liter)': 'سورج مکھی تیل (1 لیٹر)',
            'Mustard Oil (1 Liter)': 'سرسوں کا خالص تیل (1 لیٹر)',
            'Pure Honey (250ml)': 'خالص قدرتی شہد (250 ملی)',
            'Pure Honey (500ml)': 'خالص قدرتی شہد (500 ملی)',
            'Pure Honey (1kg)': 'خالص قدرتی شہد (1 کلو)',
            'Chia Seeds (100g)': 'چیا سیڈز / تخم شربتی (100 گرام)',
            'Pumpkin Seeds (100g)': 'کدو کے بیج (100 گرام)',
            'Sunflower Seeds (100g)': 'سورج مکھی کے بیج (100 گرام)',
            'Sesame Seeds (100g)': 'سفید تل (100 گرام)',
            'Flax Seeds (100g)': 'السی کے بیج (100 گرام)',
            'Kalonji (100g)': 'خالص کلونجی (100 گرام)',
            'Sonf (Fennel Seeds) (100g)': 'دیسی سونف (100 گرام)',
            'Dal Moong Wash (500g)': 'دال مونگ دھلی (500 گرام)',
            'Moong Sabat (500g)': 'مونگ ثابت (500 گرام)',
            'Black Chana (500g)': 'کالا چنا (500 گرام)',
            'Dal Chana (500g)': 'دال چنا (500 گرام)',
            'Masoor Moti (500g)': 'مسور موٹی (500 گرام)',
            'Masoor Break (500g)': 'دال مسور باریک (500 گرام)',
            'Dal Masoor Red Moti (500g)': 'دال مسور لال موٹی (500 گرام)',
            'Dal Masoor Red Break (500g)': 'دال مسور لال باریک (500 گرام)',
            'Dal Mash Wash (500g)': 'دال ماش دھلی (500 گرام)',
            'Dal Mash Chilka (500g)': 'دال ماش چھلکا (500 گرام)',
            'Channa Safeed Mota (500g)': 'سفید چنا موٹا / کابلی (500 گرام)',
            'Channa Safeed (500g)': 'سفید چنا (500 گرام)',
            'Laung (Clove) (25g)': 'لونگ (25 گرام)',
            'Daar Cheeni (Cinnamon) (100g)': 'دار چینی (100 گرام)',
            'Bari Elaichi (Black Cardamom) (25g)': 'بڑی الائچی (25 گرام)',
            'Sabz Elaichi (Green Cardamom) (25g)': 'سبز الائچی (25 گرام)',
            'Kali Mirch (Black Pepper) (100g)': 'کالی مرچ ثابت (100 گرام)',
            'Zeera (Cumin Seeds) (100g)': 'سفید زیرہ (100 گرام)',
            'Laal Mirch Powder (100g)': 'لال مرچ پاؤڈر (100 گرام)',
            'Kuti Laal Mirch (Crushed Red Chilli) (100g)': 'کٹی لال مرچ / دھڑا مرچ (100 گرام)',
            'Haldi Powder (Turmeric) (100g)': 'ہلدی پاؤڈر (100 گرام)',
            'Garam Masala (50g)': 'گرم مصالحہ مکس (50 گرام)',
            'Kala Namak (Black Salt) (100g)': 'کالا نمک (100 گرام)',
            'White Salt (800g)': 'سفید نمک (800 گرام)',
            'Pink Salt (800g)': 'ہمالین پنک سالٹ (800 گرام)',
            'Mango Pickle (700ml)': 'آم کا اچار (700 ملی)',
            'Mango Pickle (900ml)': 'آم کا اچار (900 ملی)',
            'Mixed Pickle (500ml)': 'مکس اچار (500 ملی)',
            'Green Chilli Pickle (500ml)': 'ہری مرچ کا اچار (500 ملی)',
            'Lahsun Pickle (Garlic Pickle) (500ml)': 'لہسن کا اچار (500 ملی)'
        },

        getUrduName(name) {
            return this.urduProductNames[name] || '';
        },

        togglePriceView() {
            this.showCostPrice = !this.showCostPrice;
            try {
                localStorage.setItem('pos_show_cost', this.showCostPrice);
            } catch (e) {}
        },

        initPos() {
            this.filteredProducts = [...this.allProducts];
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },

        setCategory(catId) {
            this.activeCategory = catId;
            this.filterProducts();
        },

        filterProducts() {
            let q = this.searchQuery.toLowerCase().trim();
            this.filteredProducts = this.allProducts.filter(p => {
                let matchCat = this.activeCategory === null || p.category_id === this.activeCategory;
                let urName = (this.urduProductNames[p.name] || '').toLowerCase();
                let matchQuery = !q || 
                    p.name.toLowerCase().includes(q) || 
                    urName.includes(q) ||
                    (p.barcode && p.barcode.toLowerCase().includes(q));
                return matchCat && matchQuery;
            });
        },

        handleBarcodeEnter() {
            let q = this.searchQuery.trim();
            if (!q) return;

            let exactMatch = this.allProducts.find(p => p.barcode && p.barcode.toLowerCase() === q.toLowerCase());
            if (exactMatch) {
                this.addToCart(exactMatch);
                this.searchQuery = '';
                this.filterProducts();
                return;
            }

            if (this.filteredProducts.length === 1) {
                this.addToCart(this.filteredProducts[0]);
                this.searchQuery = '';
                this.filterProducts();
            }
        },

        addToCart(product) {
            let existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity = Math.round((existing.quantity + 1) * 100) / 100;
                existing.subtotal = Math.round((existing.quantity * existing.price) * 100) / 100;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    unit: product.unit,
                    price: parseFloat(product.sale_price),
                    cost: parseFloat(product.purchase_price),
                    quantity: 1,
                    subtotal: parseFloat(product.sale_price)
                });
            }

            this.cashGiven = this.calculateGrandTotal();
        },

        increaseQty(index) {
            this.cart[index].quantity = Math.round((this.cart[index].quantity + 1) * 100) / 100;
            this.updateLineTotal(index);
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity = Math.round((this.cart[index].quantity - 1) * 100) / 100;
                this.updateLineTotal(index);
            } else {
                this.removeFromCart(index);
            }
        },

        updateLineTotal(index) {
            let item = this.cart[index];
            if (item.quantity <= 0) item.quantity = 0.01;
            item.subtotal = Math.round((item.quantity * item.price) * 100) / 100;
            this.cashGiven = this.calculateGrandTotal();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.cashGiven = this.calculateGrandTotal();
        },

        clearCart() {
            this.cart = [];
            this.discount = 0;
            this.cashGiven = 0;
            this.customerName = '';
            this.customerPhone = '';
            if (this.$refs.searchInput) this.$refs.searchInput.focus();
        },

        calculateSubtotal() {
            return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
        },

        calculateGrandTotal() {
            let sub = this.calculateSubtotal();
            let disc = parseFloat(this.discount) || 0;
            return Math.max(0, sub - disc);
        },

        calculateChange() {
            let total = this.calculateGrandTotal();
            let paid = parseFloat(this.cashGiven) || 0;
            return Math.max(0, paid - total);
        },

        formatNumber(val) {
            if (val === null || val === undefined) return '0';
            return parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },

        handleShortcuts(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                if (this.cart.length > 0 && !this.showSuccessModal) {
                    this.processCheckout();
                } else if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            } else if (e.key === 'Escape') {
                if (this.showSuccessModal) {
                    this.closeSuccessModal();
                }
            }
        },

        async processCheckout() {
            if (this.cart.length === 0 || this.isProcessing) return;

            this.isProcessing = true;

            const payload = {
                items: this.cart.map(i => ({
                    id: i.id,
                    quantity: i.quantity,
                    price: i.price
                })),
                customer_name: this.customerName || 'Walk-in Customer',
                customer_phone: this.customerPhone || null,
                discount: parseFloat(this.discount) || 0,
                payment_method: this.paymentMethod,
                cash_given: parseFloat(this.cashGiven) || this.calculateGrandTotal(),
            };

            try {
                const response = await fetch("{{ route('pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                if (response.status === 401 || response.status === 419) {
                    alert('آپ کا سیشن ختم ہو گیا ہے۔ برائے کرم صفحہ ریفریش کر کے دوبارہ لاگ ان فرمائیں۔');
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                let data;
                const rawText = await response.text();
                try {
                    data = JSON.parse(rawText);
                } catch(e) {
                    alert('Server returned non-JSON response. Please refresh the page.');
                    return;
                }

                if (response.ok && data.success) {
                    this.lastSale = data.sale;
                    this.whatsappUrl = data.whatsapp_url;
                    this.whatsappText = data.whatsapp_text;
                    this.showSuccessModal = true;
                    this.clearCart();
                    this.printReceiptDirectly(data.sale.id);
                } else {
                    alert('Error: ' + (data.message || 'Failed to process sale.'));
                }
            } catch (err) {
                alert('Connection error: ' + err.message);
            } finally {
                this.isProcessing = false;
            }
        },

        printReceiptDirectly(saleId) {
            if (saleId && typeof window.printReceiptDirect === 'function') {
                window.printReceiptDirect(saleId);
            }
        },

        closeSuccessModal() {
            this.showSuccessModal = false;
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        }
    };
}
</script>
@endsection
