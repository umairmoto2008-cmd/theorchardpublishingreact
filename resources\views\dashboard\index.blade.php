@extends('layouts.app')

@section('title', 'Financial Command Center & Profit/Loss - Hashmi Grains')
@section('header_title', 'Business Intelligence & Net Profit/Loss Dashboard')

@section('styles')
<style>
    /* Timeframe Selector Pills & Custom Controls */
    .timeframe-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: #ffffff;
        padding: 0.6rem 0.85rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }

    .timeframe-presets {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .timeframe-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.85rem;
        border-radius: var(--radius-md);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--gray-600);
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .timeframe-pill:hover {
        color: var(--gray-900);
        background: var(--gray-100);
    }

    .timeframe-pill.active {
        background: #0f5132;
        color: #ffffff;
        border-color: #0f5132;
        box-shadow: 0 4px 10px rgba(15, 81, 50, 0.25);
    }

    .timeframe-pill.active i {
        color: #4ade80;
    }

    .timeframe-pill.custom-active {
        background: linear-gradient(135deg, #065f46 0%, #0f5132 100%) !important;
        color: #ffffff !important;
        border: 1px solid #10b981 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
    }

    .timeframe-custom-box {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        background: #f8fafc;
        padding: 0.3rem 0.6rem;
        border-radius: var(--radius-md);
        border: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    .custom-label-badge {
        font-size: 0.78rem;
        font-weight: 800;
        color: #0f5132;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
        padding: 0 0.2rem;
    }

    .custom-select-input {
        padding: 0.38rem 0.65rem;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: var(--gray-800);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        transition: all 0.2s;
    }

    .custom-select-input:focus {
        border-color: #0f5132;
        box-shadow: 0 0 0 2px rgba(15, 81, 50, 0.15);
    }

    .btn-apply-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        background: #0f5132;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-apply-custom:hover {
        background: #0a3d24;
        box-shadow: 0 2px 6px rgba(15, 81, 50, 0.3);
    }

    .btn-apply-custom:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Executive Hero Banner */
    .executive-hero {
        background: linear-gradient(135deg, #062817 0%, #0a3d24 50%, #0f5132 100%);
        border-radius: var(--radius-xl);
        color: #ffffff;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(15, 81, 50, 0.3);
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap: 1.25rem;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .executive-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(74, 222, 128, 0.15) 0%, rgba(0,0,0,0) 70%);
        pointer-events: none;
    }

    .hero-main-stat {
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        padding-right: 1.25rem;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(255, 255, 255, 0.12);
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #86efac;
        margin-bottom: 0.5rem;
    }

    .hero-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #cbd5e1;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .hero-value {
        font-family: 'Outfit', sans-serif;
        font-size: 2.1rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.15;
        margin: 0.25rem 0;
    }

    .hero-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .hero-mini-box {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-md);
        padding: 0.85rem 1rem;
        backdrop-filter: blur(4px);
    }

    .hero-mini-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.04em;
    }

    .hero-mini-val {
        font-family: 'Outfit', sans-serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: #ffffff;
        margin-top: 0.15rem;
    }

    .hero-mini-sub {
        font-size: 0.7rem;
        color: #cbd5e1;
        margin-top: 0.15rem;
    }

    /* 8-Card Metrics Grid */
    .metrics-grid-8 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .metric-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: 1.15rem;
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .card-border-emerald::before { background: linear-gradient(90deg, #10b981, #059669); }
    .card-border-blue::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
    .card-border-amber::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .card-border-rose::before { background: linear-gradient(90deg, #f43f5e, #e11d48); }
    .card-border-purple::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }
    .card-border-teal::before { background: linear-gradient(90deg, #14b8a6, #0d9488); }
    .card-border-indigo::before { background: linear-gradient(90deg, #6366f1, #4f46e5); }
    .card-border-orange::before { background: linear-gradient(90deg, #f97316, #ea580c); }

    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .icon-emerald { background: #ecfdf5; color: #059669; }
    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-amber { background: #fffbeb; color: #d97706; }
    .icon-rose { background: #fff1f2; color: #e11d48; }
    .icon-purple { background: #faf5ff; color: #7c3aed; }
    .icon-teal { background: #f0fdfa; color: #0d9488; }
    .icon-indigo { background: #eef2ff; color: #4f46e5; }
    .icon-orange { background: #fff7ed; color: #ea580c; }

    .metric-data {
        flex: 1;
        min-width: 0;
    }

    .metric-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.15rem;
    }

    .metric-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--gray-500);
    }

    .metric-ur-label {
        font-family: 'Noto Nastaliq Urdu', 'Noto Sans Arabic', sans-serif;
        font-size: 0.75rem;
        color: var(--gray-400);
    }

    .metric-value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1.2;
    }

    .metric-sub {
        font-size: 0.72rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.35rem;
    }

    /* P&L Comparison Table */
    .pnl-table-wrap {
        background: #ffffff;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .pnl-matrix-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .pnl-matrix-table th {
        background: #f8fafc;
        color: var(--gray-700);
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.85rem 1rem;
        border-bottom: 2px solid var(--gray-200);
        text-align: right;
    }

    .pnl-matrix-table th:first-child {
        text-align: left;
    }

    .pnl-matrix-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        text-align: right;
        color: var(--gray-800);
        vertical-align: middle;
    }

    .pnl-matrix-table td:first-child {
        text-align: left;
        font-weight: 600;
    }

    .pnl-matrix-table tr:hover {
        background: #f8fafc;
    }

    .pnl-highlight-row {
        background: #ecfdf5 !important;
        font-weight: 800 !important;
    }

    .pnl-highlight-row td {
        color: #065f46 !important;
        font-size: 0.95rem;
        border-top: 2px solid #10b981;
        border-bottom: 2px solid #10b981;
    }

    .pnl-highlight-row td:first-child {
        font-weight: 800 !important;
    }

    /* Layout Grids */
    .dashboard-2col {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-2col-equal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Payment and Category Progress Bars */
    .stat-progress-item {
        margin-bottom: 0.85rem;
    }

    .stat-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }

    .stat-progress-bar {
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }

    .stat-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    /* Chart filter buttons */
    .chart-btn-group {
        display: flex;
        gap: 0.35rem;
    }

    .chart-pill-btn {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        border: 1px solid var(--gray-300);
        background: white;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .chart-pill-btn.active {
        background: #0f5132;
        color: white;
        border-color: #0f5132;
    }

    @media (max-width: 1200px) {
        .executive-hero {
            grid-template-columns: 1fr 1fr;
        }
        .metrics-grid-8 {
            grid-template-columns: repeat(2, 1fr);
        }
        .dashboard-2col, .dashboard-2col-equal {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .executive-hero {
            grid-template-columns: 1fr;
        }
        .metrics-grid-8 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div x-data="dashboardManager()">

    <!-- 1. Interactive Timeframe Switcher Bar with Year & Custom Month/Year Filter -->
    <div class="timeframe-bar">
        <div class="timeframe-presets">
            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--gray-400); padding: 0 0.4rem;">
                <i class="fa-solid fa-filter"></i> Timeframe:
            </span>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'today' }" @click="activeTab = 'today'">
                <i class="fa-solid fa-bolt"></i> Today
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'yesterday' }" @click="activeTab = 'yesterday'">
                <i class="fa-solid fa-clock-rotate-left"></i> Yesterday
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'week' }" @click="activeTab = 'week'">
                <i class="fa-solid fa-calendar-week"></i> This Week
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'month' }" @click="activeTab = 'month'">
                <i class="fa-solid fa-calendar-days"></i> This Month
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'last_month' }" @click="activeTab = 'last_month'">
                <i class="fa-solid fa-calendar-check"></i> Last Month
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'this_year' }" @click="activeTab = 'this_year'">
                <i class="fa-solid fa-calendar"></i> This Year ({{ now()->year }})
            </button>
            <button type="button" class="timeframe-pill" :class="{ 'active': activeTab === 'all_time' }" @click="activeTab = 'all_time'">
                <i class="fa-solid fa-building-columns"></i> All-Time
            </button>
            <!-- Dynamic Custom Tab Active Indicator -->
            <button type="button" x-show="activeTab === 'custom'" class="timeframe-pill custom-active" @click="activeTab = 'custom'">
                <i class="fa-solid fa-calendar-check" style="color: #86efac;"></i>
                <span x-text="customLabel || 'Custom Selection'"></span>
            </button>
        </div>

        <!-- Custom Month & Year Selector -->
        <div class="timeframe-custom-box">
            <span class="custom-label-badge">
                <i class="fa-solid fa-sliders"></i> Custom Filter:
            </span>
            
            <select x-model="selectedMonth" class="custom-select-input" title="Select Month">
                <option value="all">📅 Full Year (All Months)</option>
                <option value="1">01 - January</option>
                <option value="2">02 - February</option>
                <option value="3">03 - March</option>
                <option value="4">04 - April</option>
                <option value="5">05 - May</option>
                <option value="6">06 - June</option>
                <option value="7">07 - July</option>
                <option value="8">08 - August</option>
                <option value="9">09 - September</option>
                <option value="10">10 - October</option>
                <option value="11">11 - November</option>
                <option value="12">12 - December</option>
            </select>

            <select x-model="selectedYear" class="custom-select-input" title="Select Year">
                @foreach($allYears as $yr)
                    <option value="{{ $yr }}">Year {{ $yr }}</option>
                @endforeach
            </select>

            <button type="button" @click="fetchCustomPeriod()" class="btn-apply-custom" :disabled="loading" title="Filter Records">
                <span x-show="!loading" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class="fa-solid fa-magnifying-glass-chart"></i> View Stats
                </span>
                <span x-show="loading" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class="fa-solid fa-spinner fa-spin"></i> Loading...
                </span>
            </button>
        </div>
    </div>

    <!-- 2. Dynamic Hero Executive Profit & Loss Card -->
    <div class="executive-hero">
        <div class="hero-main-stat">
            <div class="hero-badge">
                <i class="fa-solid fa-circle-dot"></i>
                <span x-text="currentData.title + ' Financial Pulse'"></span>
            </div>
            <div class="hero-title">
                <span>Net Bottom-Line Profit / (Loss)</span>
                <span class="urdu-title" style="font-size: 0.85rem; color: #86efac;">خالص بچت و منافع</span>
            </div>
            <div class="hero-value" :style="currentData.net_profit >= 0 ? 'color: #4ade80;' : 'color: #f87171;'">
                <span x-text="'Rs. ' + formatNumber(currentData.net_profit)"></span>
            </div>
            <div class="hero-sub">
                <span style="font-weight: 700; color: #86efac;" x-text="currentData.net_margin_pct + '% Net Margin'"></span>
                &bull;
                <span x-text="currentData.subtitle"></span>
            </div>
        </div>

        <div class="hero-mini-box">
            <div class="hero-mini-label">Gross Sales Revenue (کل فروخت)</div>
            <div class="hero-mini-val" x-text="'Rs. ' + formatNumber(currentData.revenue)"></div>
            <div class="hero-mini-sub">
                <span x-text="currentData.orders_count + ' Invoices'"></span>
                &bull;
                <span x-text="'Avg: Rs. ' + formatNumber(currentData.avg_order)"></span>
            </div>
        </div>

        <div class="hero-mini-box">
            <div class="hero-mini-label">Gross Profit (خام منافع)</div>
            <div class="hero-mini-val" style="color: #67e8f9;" x-text="'Rs. ' + formatNumber(currentData.gross_profit)"></div>
            <div class="hero-mini-sub">
                <span x-text="currentData.gross_margin_pct + '% Margin'"></span>
                &bull;
                <span x-text="'COGS: Rs. ' + formatNumber(currentData.cogs)"></span>
            </div>
        </div>

        <div class="hero-mini-box">
            <div class="hero-mini-label">Operating Expenses (اخراجات)</div>
            <div class="hero-mini-val" style="color: #fda4af;" x-text="'Rs. ' + formatNumber(currentData.expenses)"></div>
            <div class="hero-mini-sub">
                <span x-text="'Discounts: Rs. ' + formatNumber(currentData.discounts)"></span>
                &bull;
                <span x-text="'Returns: Rs. ' + formatNumber(currentData.returns_refund)"></span>
            </div>
        </div>
    </div>

    <!-- 3. Dynamic 8-Card Breakdown Grid -->
    <div class="metrics-grid-8">
        <!-- 1. Gross Revenue -->
        <div class="metric-card card-border-emerald">
            <div class="metric-icon icon-emerald">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Gross Sales Revenue</div>
                    <div class="metric-ur-label">کل فروخت</div>
                </div>
                <div class="metric-value" x-text="'Rs. ' + formatNumber(currentData.revenue)"></div>
                <div class="metric-sub">
                    <span style="color: #16a34a; font-weight: 700;"><i class="fa-solid fa-file-invoice"></i> <span x-text="currentData.orders_count"></span> Bills</span>
                    <span>&bull; Avg: Rs. <span x-text="formatNumber(currentData.avg_order)"></span></span>
                </div>
            </div>
        </div>

        <!-- 2. Cost of Goods Sold (COGS) -->
        <div class="metric-card card-border-blue">
            <div class="metric-icon icon-blue">
                <i class="fa-solid fa-cart-flatbed"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Cost of Goods (COGS)</div>
                    <div class="metric-ur-label">خرید لاگت</div>
                </div>
                <div class="metric-value" style="color: #2563eb;" x-text="'Rs. ' + formatNumber(currentData.cogs)"></div>
                <div class="metric-sub">
                    <span>Base Wholesale Cost</span>
                    <span>&bull;</span>
                    <span x-text="currentData.revenue > 0 ? (100 - currentData.gross_margin_pct).toFixed(1) + '% Cost' : '0%'"></span>
                </div>
            </div>
        </div>

        <!-- 3. Gross Profit -->
        <div class="metric-card card-border-teal">
            <div class="metric-icon icon-teal">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Gross Margin Profit</div>
                    <div class="metric-ur-label">خام منافع</div>
                </div>
                <div class="metric-value" style="color: #0d9488;" x-text="'Rs. ' + formatNumber(currentData.gross_profit)"></div>
                <div class="metric-sub">
                    <span style="font-weight: 700; color: #0d9488;" x-text="currentData.gross_margin_pct + '% Markup'"></span>
                    <span>&bull; Before Overheads</span>
                </div>
            </div>
        </div>

        <!-- 4. Operating Expenses -->
        <div class="metric-card card-border-rose">
            <div class="metric-icon icon-rose">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Store Expenses</div>
                    <div class="metric-ur-label">دکان کے اخراجات</div>
                </div>
                <div class="metric-value" style="color: #dc2626;" x-text="'Rs. ' + formatNumber(currentData.expenses)"></div>
                <div class="metric-sub">
                    <a href="{{ route('expenses.index') }}" style="color: #dc2626; text-decoration: none; font-weight: 700;">
                        Manage Expenses &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. Net Bottom-Line Profit -->
        <div class="metric-card card-border-amber">
            <div class="metric-icon icon-amber">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Net Bottom Profit</div>
                    <div class="metric-ur-label">خالص منافع</div>
                </div>
                <div class="metric-value" :style="currentData.net_profit >= 0 ? 'color: #16a34a;' : 'color: #dc2626;'" x-text="'Rs. ' + formatNumber(currentData.net_profit)"></div>
                <div class="metric-sub">
                    <span style="font-weight: 700;" :style="currentData.net_profit >= 0 ? 'color: #16a34a;' : 'color: #dc2626;'" x-text="currentData.net_margin_pct + '% Net Margin'"></span>
                    <span>&bull; Pocket Profit</span>
                </div>
            </div>
        </div>

        <!-- 6. Discounts Given -->
        <div class="metric-card card-border-purple">
            <div class="metric-icon icon-purple">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Customer Discounts</div>
                    <div class="metric-ur-label">دی گئی چھوٹ</div>
                </div>
                <div class="metric-value" style="color: #7c3aed;" x-text="'Rs. ' + formatNumber(currentData.discounts)"></div>
                <div class="metric-sub">
                    <span>Subtracted from retail prices</span>
                </div>
            </div>
        </div>

        <!-- 7. Customer Returns & Refunds -->
        <div class="metric-card card-border-orange">
            <div class="metric-icon icon-orange">
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Returns & Refund Loss</div>
                    <div class="metric-ur-label">واپسی و ریفنڈ</div>
                </div>
                <div class="metric-value" style="color: #ea580c;" x-text="'Rs. ' + formatNumber(currentData.returns_refund)"></div>
                <div class="metric-sub">
                    <span x-text="currentData.returns_count + ' Returns Processed'"></span>
                    <span>&bull;</span>
                    <a href="{{ route('returns.index') }}" style="color: #ea580c; text-decoration: none; font-weight: 700;">View &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 8. Cash Drawer Balance -->
        <div class="metric-card card-border-indigo">
            <div class="metric-icon icon-indigo">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="metric-data">
                <div class="metric-label-row">
                    <div class="metric-label">Cash In Drawer</div>
                    <div class="metric-ur-label">گلے میں نقد</div>
                </div>
                <div class="metric-value" style="color: #4f46e5;" x-text="'Rs. ' + formatNumber(currentData.cash_drawer)"></div>
                <div class="metric-sub">
                    <span x-text="'Cash In: Rs. ' + formatNumber(currentData.cash_sales)"></span>
                    <span>&bull;</span>
                    <span x-text="'Out: Rs. ' + formatNumber(currentData.expenses)"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Complete Side-by-Side P&L Income Statement Matrix -->
    <div class="pnl-table-wrap">
        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid var(--gray-200);">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 0.5rem;">
                <h3 class="card-title">
                    <i class="fa-solid fa-scale-balanced" style="color: #0f5132;"></i>
                    <span>Master Income Statement & P&L Comparative Matrix</span>
                </h3>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" onclick="window.print()" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-print"></i> Print P&L Statement
                    </button>
                    <span style="font-size: 0.76rem; font-weight: 700; color: #0f5132; background: #ecfdf5; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">
                        <i class="fa-solid fa-shield-check"></i> Multi-Timeframe Store Audit
                    </span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="pnl-matrix-table">
                <thead>
                    <tr>
                        <th style="width: 24%;">Financial Metric / Line Item</th>
                        <th>Today</th>
                        <th>Yesterday</th>
                        <th>This Week</th>
                        <th>This Month</th>
                        <th>Last Month</th>
                        <th>This Year ({{ now()->year }})</th>
                        <th>All-Time Store</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 1. Revenue -->
                    <tr>
                        <td><i class="fa-solid fa-circle-plus" style="color: #10b981; font-size: 0.75rem; margin-right: 0.4rem;"></i> Gross Sales Revenue (کل فروخت)</td>
                        <td style="font-weight: 700;">Rs. {{ number_format($todayMetrics['revenue'], 0) }}</td>
                        <td>Rs. {{ number_format($yesterdayMetrics['revenue'], 0) }}</td>
                        <td>Rs. {{ number_format($weekMetrics['revenue'], 0) }}</td>
                        <td style="font-weight: 700; color: #0284c7;">Rs. {{ number_format($monthMetrics['revenue'], 0) }}</td>
                        <td>Rs. {{ number_format($lastMonthMetrics['revenue'], 0) }}</td>
                        <td style="font-weight: 700; color: #059669;">Rs. {{ number_format($thisYearMetrics['revenue'], 0) }}</td>
                        <td style="font-weight: 800;">Rs. {{ number_format($allTimeMetrics['revenue'], 0) }}</td>
                    </tr>

                    <!-- 2. COGS -->
                    <tr>
                        <td><i class="fa-solid fa-circle-minus" style="color: #94a3b8; font-size: 0.75rem; margin-right: 0.4rem;"></i> Cost of Goods Sold (COGS - لاگت)</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($todayMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($yesterdayMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($weekMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($monthMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($lastMonthMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($thisYearMetrics['cogs'], 0) }}</td>
                        <td style="color: #64748b;">- Rs. {{ number_format($allTimeMetrics['cogs'], 0) }}</td>
                    </tr>

                    <!-- 3. Gross Profit -->
                    <tr style="background: #fafafa; font-weight: 700;">
                        <td style="color: #0d9488;"><i class="fa-solid fa-arrow-trend-up" style="margin-right: 0.4rem;"></i> Gross Sales Profit (خام منافع)</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($todayMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($yesterdayMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($weekMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($monthMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($lastMonthMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($thisYearMetrics['gross_profit'], 0) }}</td>
                        <td style="color: #0d9488;">Rs. {{ number_format($allTimeMetrics['gross_profit'], 0) }}</td>
                    </tr>

                    <!-- 4. Gross Margin % -->
                    <tr>
                        <td style="color: var(--gray-500); padding-left: 2rem;">Gross Margin %</td>
                        <td>{{ $todayMetrics['gross_margin_pct'] }}%</td>
                        <td>{{ $yesterdayMetrics['gross_margin_pct'] }}%</td>
                        <td>{{ $weekMetrics['gross_margin_pct'] }}%</td>
                        <td style="font-weight: 700;">{{ $monthMetrics['gross_margin_pct'] }}%</td>
                        <td>{{ $lastMonthMetrics['gross_margin_pct'] }}%</td>
                        <td style="font-weight: 700;">{{ $thisYearMetrics['gross_margin_pct'] }}%</td>
                        <td>{{ $allTimeMetrics['gross_margin_pct'] }}%</td>
                    </tr>

                    <!-- 5. Operating Expenses -->
                    <tr>
                        <td><i class="fa-solid fa-circle-minus" style="color: #ef4444; font-size: 0.75rem; margin-right: 0.4rem;"></i> Store Operating Expenses (اخراجات)</td>
                        <td style="color: #dc2626;">- Rs. {{ number_format($todayMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626;">- Rs. {{ number_format($yesterdayMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626;">- Rs. {{ number_format($weekMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626; font-weight: 700;">- Rs. {{ number_format($monthMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626;">- Rs. {{ number_format($lastMonthMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626; font-weight: 700;">- Rs. {{ number_format($thisYearMetrics['expenses'], 0) }}</td>
                        <td style="color: #dc2626;">- Rs. {{ number_format($allTimeMetrics['expenses'], 0) }}</td>
                    </tr>

                    <!-- 6. Discounts Given -->
                    <tr>
                        <td><i class="fa-solid fa-tags" style="color: #8b5cf6; font-size: 0.75rem; margin-right: 0.4rem;"></i> Customer Discounts Allowed (چھوٹ)</td>
                        <td>Rs. {{ number_format($todayMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($yesterdayMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($weekMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($monthMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($lastMonthMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($thisYearMetrics['discounts'], 0) }}</td>
                        <td>Rs. {{ number_format($allTimeMetrics['discounts'], 0) }}</td>
                    </tr>

                    <!-- 7. Customer Returns -->
                    <tr>
                        <td><i class="fa-solid fa-arrow-rotate-left" style="color: #ea580c; font-size: 0.75rem; margin-right: 0.4rem;"></i> Customer Returns Refunded (واپسی)</td>
                        <td>Rs. {{ number_format($todayMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($yesterdayMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($weekMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($monthMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($lastMonthMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($thisYearMetrics['returns_refund'], 0) }}</td>
                        <td>Rs. {{ number_format($allTimeMetrics['returns_refund'], 0) }}</td>
                    </tr>

                    <!-- 8. Net Profit Highlight Row -->
                    <tr class="pnl-highlight-row">
                        <td><i class="fa-solid fa-sack-dollar" style="color: #10b981; margin-right: 0.4rem;"></i> NET BOTTOM-LINE PROFIT / (LOSS) (خالص منافع)</td>
                        <td style="font-family: 'Outfit'; font-size: 1.05rem; color: {{ $todayMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($todayMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.05rem; color: {{ $yesterdayMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($yesterdayMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.05rem; color: {{ $weekMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($weekMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.15rem; color: {{ $monthMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($monthMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.05rem; color: {{ $lastMonthMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($lastMonthMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.15rem; color: {{ $thisYearMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($thisYearMetrics['net_profit'], 0) }}
                        </td>
                        <td style="font-family: 'Outfit'; font-size: 1.15rem; color: {{ $allTimeMetrics['net_profit'] >= 0 ? '#065f46' : '#991b1b' }} !important;">
                            Rs. {{ number_format($allTimeMetrics['net_profit'], 0) }}
                        </td>
                    </tr>

                    <!-- 9. Net Margin % -->
                    <tr>
                        <td style="font-weight: 700; color: var(--gray-700);">Net Profit Margin % (بچت کی شرح)</td>
                        <td style="font-weight: 700; color: {{ $todayMetrics['net_margin_pct'] >= 0 ? '#16a34a' : '#dc2626' }};">{{ $todayMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 700;">{{ $yesterdayMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 700;">{{ $weekMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 800; color: #16a34a;">{{ $monthMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 700;">{{ $lastMonthMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 800; color: #16a34a;">{{ $thisYearMetrics['net_margin_pct'] }}%</td>
                        <td style="font-weight: 800; color: #7c3aed;">{{ $allTimeMetrics['net_margin_pct'] }}%</td>
                    </tr>

                    <!-- 10. Orders Count -->
                    <tr>
                        <td><i class="fa-solid fa-receipt" style="color: var(--gray-400); margin-right: 0.4rem;"></i> Total Invoices / Orders (کل بل)</td>
                        <td>{{ $todayMetrics['orders_count'] }}</td>
                        <td>{{ $yesterdayMetrics['orders_count'] }}</td>
                        <td>{{ $weekMetrics['orders_count'] }}</td>
                        <td style="font-weight: 700;">{{ $monthMetrics['orders_count'] }}</td>
                        <td>{{ $lastMonthMetrics['orders_count'] }}</td>
                        <td style="font-weight: 700;">{{ $thisYearMetrics['orders_count'] }}</td>
                        <td style="font-weight: 800;">{{ $allTimeMetrics['orders_count'] }}</td>
                    </tr>

                    <!-- 11. Average Order Value -->
                    <tr>
                        <td><i class="fa-solid fa-basket-shopping" style="color: var(--gray-400); margin-right: 0.4rem;"></i> Average Order Value (AOV - فی گاہک بل)</td>
                        <td>Rs. {{ number_format($todayMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($yesterdayMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($weekMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($monthMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($lastMonthMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($thisYearMetrics['avg_order'], 0) }}</td>
                        <td>Rs. {{ number_format($allTimeMetrics['avg_order'], 0) }}</td>
                    </tr>

                    <!-- 12. Cash Drawer Reconciled -->
                    <tr style="background: #f8fafc;">
                        <td><i class="fa-solid fa-vault" style="color: #4f46e5; margin-right: 0.4rem;"></i> Net Cash In Drawer (گلے میں موجود کیش)</td>
                        <td style="font-weight: 700; color: #4f46e5;">Rs. {{ number_format($todayMetrics['cash_drawer'], 0) }}</td>
                        <td>Rs. {{ number_format($yesterdayMetrics['cash_drawer'], 0) }}</td>
                        <td>Rs. {{ number_format($weekMetrics['cash_drawer'], 0) }}</td>
                        <td style="font-weight: 700; color: #4f46e5;">Rs. {{ number_format($monthMetrics['cash_drawer'], 0) }}</td>
                        <td>Rs. {{ number_format($lastMonthMetrics['cash_drawer'], 0) }}</td>
                        <td style="font-weight: 700; color: #4f46e5;">Rs. {{ number_format($thisYearMetrics['cash_drawer'], 0) }}</td>
                        <td>Rs. {{ number_format($allTimeMetrics['cash_drawer'], 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Chart & Inventory Health Overview -->
    <div class="dashboard-2col">
        <!-- 14-Day Sales, Expenses & Net Profit Trend -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-chart-column" style="color: #16a34a;"></i>
                    <span>14-Day Revenue, Expense & Profit Trend</span>
                </h3>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Inventory Valuation & Health Center -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-warehouse" style="color: #0d9488;"></i>
                    <span>Stock Health & Valuation</span>
                </h3>
                <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Catalog &rarr;</a>
            </div>
            <div class="card-body" style="padding: 1rem 1.25rem;">
                <!-- Cost Valuation -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px dashed var(--gray-200);">
                    <div style="font-size: 0.8rem; color: var(--gray-600);">
                        <i class="fa-solid fa-coins" style="color: #d97706; margin-right: 0.35rem;"></i>
                        Stock Value (Wholesale Cost / خرید لاگت)
                    </div>
                    <div style="font-family: 'Outfit'; font-weight: 800; font-size: 1.05rem; color: var(--gray-900);">
                        Rs. {{ number_format($stockValuationCost, 0) }}
                    </div>
                </div>

                <!-- Retail Valuation -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px dashed var(--gray-200);">
                    <div style="font-size: 0.8rem; color: var(--gray-600);">
                        <i class="fa-solid fa-tag" style="color: #2563eb; margin-right: 0.35rem;"></i>
                        Stock Value (Retail Sales / متوقع فروخت)
                    </div>
                    <div style="font-family: 'Outfit'; font-weight: 800; font-size: 1.05rem; color: #2563eb;">
                        Rs. {{ number_format($stockValuationSale, 0) }}
                    </div>
                </div>

                <!-- Potential Unrealized Profit -->
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin: 0.75rem 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">
                            Potential Unrealized Profit (متوقع منافع)
                        </div>
                        <span style="font-size: 0.72rem; font-weight: 800; background: #10b981; color: white; padding: 2px 6px; border-radius: 12px;">
                            +{{ $potentialStockMarginPct }}% Margin
                        </span>
                    </div>
                    <div style="font-family: 'Outfit'; font-size: 1.35rem; font-weight: 900; color: #065f46; margin-top: 0.2rem;">
                        +Rs. {{ number_format($potentialStockProfit, 0) }}
                    </div>
                </div>

                <!-- Stock Alerts Badges -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 0.6rem 0.75rem;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Out of Stock (ختم شدہ)</div>
                        <div style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 800; color: #dc2626;">{{ $outOfStockCount }} Items</div>
                    </div>
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-md); padding: 0.6rem 0.75rem;">
                        <div style="font-size: 0.7rem; font-weight: 700; color: #92400e; text-transform: uppercase;">Low Stock (کم اسٹاک)</div>
                        <div style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 800; color: #d97706;">
                            <a href="{{ route('inventory.low_stock') }}" style="color: #d97706; text-decoration: none;">
                                {{ $lowStockCount }} Items &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Payment Methods & Monthly Expense Category Breakdown -->
    <div class="dashboard-2col-equal">
        <!-- Payment Methods Share -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-credit-card" style="color: #3b82f6;"></i>
                    <span>Payment Channels (This Month)</span>
                </h3>
            </div>
            <div class="card-body">
                @php
                    $totalMonthRev = $monthMetrics['revenue'] > 0 ? $monthMetrics['revenue'] : 1;
                @endphp
                @forelse($paymentMethodsMonth as $pm)
                    @php
                        $pct = round(($pm->total_amount / $totalMonthRev) * 100, 1);
                        $color = match($pm->payment_method) {
                            'cash' => '#10b981',
                            'card' => '#3b82f6',
                            'easypaisa' => '#14b8a6',
                            'bank_transfer' => '#8b5cf6',
                            default => '#f59e0b'
                        };
                    @endphp
                    <div class="stat-progress-item">
                        <div class="stat-progress-header">
                            <div style="display: flex; align-items: center; gap: 0.4rem; text-transform: capitalize;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $color }};"></span>
                                <span>{{ $pm->payment_method }}</span>
                                <span style="font-size: 0.72rem; color: var(--gray-400);">({{ $pm->total_count }} orders)</span>
                            </div>
                            <div>
                                <span style="font-family: 'Outfit'; font-weight: 700; color: var(--gray-900);">Rs. {{ number_format($pm->total_amount, 0) }}</span>
                                <span style="font-size: 0.72rem; color: var(--gray-500); margin-left: 0.25rem;">({{ $pct }}%)</span>
                            </div>
                        </div>
                        <div class="stat-progress-bar">
                            <div class="stat-progress-fill" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--gray-400);">No sales recorded this month</div>
                @endforelse
            </div>
        </div>

        <!-- Monthly Expense Category Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-receipt" style="color: #f43f5e;"></i>
                    <span>Operating Cost Breakdown (This Month)</span>
                </h3>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline btn-sm">Add Expense &rarr;</a>
            </div>
            <div class="card-body">
                @php
                    $totalMonthExp = $monthMetrics['expenses'] > 0 ? $monthMetrics['expenses'] : 1;
                @endphp
                @forelse($expenseCategoriesMonth as $cat)
                    @php
                        $catPct = round(($cat->total_amount / $totalMonthExp) * 100, 1);
                    @endphp
                    <div class="stat-progress-item">
                        <div class="stat-progress-header">
                            <div style="display: flex; align-items: center; gap: 0.4rem; text-transform: capitalize;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #f43f5e;"></span>
                                <span>{{ $cat->category }}</span>
                                <span style="font-size: 0.72rem; color: var(--gray-400);">({{ $cat->count }} entries)</span>
                            </div>
                            <div>
                                <span style="font-family: 'Outfit'; font-weight: 700; color: #dc2626;">Rs. {{ number_format($cat->total_amount, 0) }}</span>
                                <span style="font-size: 0.72rem; color: var(--gray-500); margin-left: 0.25rem;">({{ $catPct }}%)</span>
                            </div>
                        </div>
                        <div class="stat-progress-bar">
                            <div class="stat-progress-fill" style="width: {{ $catPct }}%; background: #f43f5e;"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--gray-400);">No expenses recorded this month</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 7. Top Profit Products vs Top Volume Movers -->
    <div class="dashboard-2col-equal">
        <!-- Top Profit Generating Items -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-trophy" style="color: #d97706;"></i>
                    <span>Top Profit-Generating Products (سب سے زیادہ منافع)</span>
                </h3>
            </div>
            <div class="card-body" style="padding: 0.5rem 1rem;">
                @forelse($topProfitProducts as $idx => $tp)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0; border-bottom: 1px solid var(--gray-100);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">
                                #{{ $idx + 1 }}
                            </div>
                            <div>
                                <div style="font-size: 0.88rem; font-weight: 700; color: var(--gray-900);">{{ $tp->product_name }}</div>
                                <div style="font-size: 0.72rem; color: var(--gray-500);">Sold: {{ $tp->total_qty }} {{ $tp->unit }} &bull; Rev: Rs. {{ number_format($tp->total_revenue, 0) }}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: 'Outfit'; font-weight: 800; color: #059669; font-size: 0.95rem;">
                                +Rs. {{ number_format($tp->total_profit, 0) }}
                            </div>
                            <div style="font-size: 0.7rem; color: var(--gray-400); font-weight: 600;">Net Line Profit</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--gray-400);">No sales recorded yet</div>
                @endforelse
            </div>
        </div>

        <!-- Top Volume Selling Products -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-boxes-packing" style="color: #0284c7;"></i>
                    <span>Fastest Selling Items by Volume (زیادہ فروخت شدہ)</span>
                </h3>
            </div>
            <div class="card-body" style="padding: 0.5rem 1rem;">
                @forelse($topVolumeProducts as $idx => $tv)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0; border-bottom: 1px solid var(--gray-100);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">
                                #{{ $idx + 1 }}
                            </div>
                            <div>
                                <div style="font-size: 0.88rem; font-weight: 700; color: var(--gray-900);">{{ $tv->product_name }}</div>
                                <div style="font-size: 0.72rem; color: var(--gray-500);">Revenue: Rs. {{ number_format($tv->total_revenue, 0) }}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-family: 'Outfit'; font-weight: 800; color: #0284c7; font-size: 0.95rem;">
                                {{ $tv->total_qty }} {{ $tv->unit }}
                            </div>
                            <div style="font-size: 0.7rem; color: var(--gray-400); font-weight: 600;">Units Sold</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: var(--gray-400);">No sales recorded yet</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 8. Recent Transactions Table -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-receipt" style="color: #0284c7;"></i>
                <span>Recent Sales Invoices (حالیہ فروخت رسیدیں)</span>
            </h3>
            <a href="{{ route('sales.index') }}" class="btn btn-outline btn-sm">View All Sales &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Items Count</th>
                        <th>Payment</th>
                        <th>Grand Total</th>
                        <th>Sales Profit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: #0f5132; text-decoration: none;">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->created_at->format('d M, h:i A') }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $sale->customer_name ?: 'Walk-in' }}</div>
                                @if($sale->customer_phone)
                                    <div style="font-size: 0.72rem; color: var(--gray-500);">{{ $sale->customer_phone }}</div>
                                @endif
                            </td>
                            <td>{{ $sale->items->count() }} items</td>
                            <td>
                                <span class="status-badge badge-info" style="text-transform: capitalize;">{{ $sale->payment_method }}</span>
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 800; font-size: 0.95rem; color: #0f5132;">
                                Rs. {{ number_format($sale->grand_total, 0) }}
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 700; color: #059669;">
                                Rs. {{ number_format($sale->profit, 0) }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.35rem;">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline btn-sm" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <button type="button" onclick="window.printReceiptDirect({{ $sale->id }})" class="btn btn-outline btn-sm" title="Thermal Print (Same Tab)">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--gray-400);">No sales recorded yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function dashboardManager() {
        return {
            activeTab: 'today',
            selectedMonth: '{{ now()->month }}',
            selectedYear: '{{ now()->year }}',
            customLabel: '',
            loading: false,
            timeframes: @json($timeframesData),
            get currentData() {
                return this.timeframes[this.activeTab] || this.timeframes['today'];
            },
            formatNumber(num) {
                if (num === null || num === undefined) return '0';
                return Number(num).toLocaleString('en-US', { maximumFractionDigits: 0 });
            },
            async fetchCustomPeriod() {
                this.loading = true;
                try {
                    const url = `{{ route('dashboard.custom-stats') }}?month=${encodeURIComponent(this.selectedMonth)}&year=${encodeURIComponent(this.selectedYear)}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('Failed to fetch stats');
                    const data = await res.json();
                    this.timeframes['custom'] = data;
                    this.customLabel = data.title;
                    this.activeTab = 'custom';
                } catch (err) {
                    console.error('Failed to load custom stats:', err);
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chartLabels = @json($chartLabels);
        const chartSales = @json($chartSales);
        const chartExpenses = @json($chartExpenses);
        const chartNetProfit = @json($chartNetProfit);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Gross Sales Revenue (Rs)',
                        data: chartSales,
                        backgroundColor: '#10b981',
                        borderRadius: 5,
                    },
                    {
                        label: 'Operating Expenses (Rs)',
                        data: chartExpenses,
                        backgroundColor: '#f43f5e',
                        borderRadius: 5,
                    },
                    {
                        label: 'Net Profit (Rs)',
                        data: chartNetProfit,
                        backgroundColor: '#3b82f6',
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return 'Rs. ' + val.toLocaleString(); }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rs. ' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
