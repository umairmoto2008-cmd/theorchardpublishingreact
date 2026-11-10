<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS & Inventory') - Hashmi Grains</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&family=Noto+Nastaliq+Urdu:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- JsBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        :root {
            --primary: #0f5132;
            --primary-dark: #0a3622;
            --primary-light: #198754;
            --primary-subtle: #d1e7dd;
            --accent-gold: #d97706;
            --accent-gold-light: #fef3c7;
            --dark: #0f172a;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-300: #d1d5db;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
            --white: #ffffff;
            --danger: #dc2626;
            --danger-subtle: #fee2e2;
            --warning: #f59e0b;
            --info: #0284c7;
            --success: #16a34a;
            --sidebar-width: 255px;
            --header-height: 64px;
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 18px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', 'Plus Jakarta Sans', sans-serif; }

        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .urdu-title { font-family: 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif !important; }

        body {
            background-color: #f1f5f9;
            color: var(--gray-800);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Backdrop on Mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 45;
        }
        .sidebar-backdrop.active { display: block; }

        /* Sidebar */
        .app-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #092c1b 0%, #061e12 100%);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }

        .sidebar-nav {
            flex: 1;
            padding: 0.85rem 0.75rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            color: #6ee7b7;
            font-weight: 800;
            letter-spacing: 0.06em;
            padding: 0.65rem 0.75rem 0.25rem;
            margin-top: 0.25rem;
            opacity: 0.75;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 0.9rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(3px);
        }

        .nav-item.active {
            color: #ffffff;
            background: linear-gradient(90deg, #15803d, #16a34a);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        .nav-item i {
            width: 20px;
            font-size: 1.05rem;
            text-align: center;
            opacity: 0.95;
        }

        .nav-item .badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
        }

        .sidebar-footer {
            padding: 0.9rem 1.15rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 0.85rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.7rem; color: #94a3b8; text-transform: capitalize; }

        /* Main Content Wrapper */
        .app-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .app-header {
            height: var(--header-height);
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .header-title-section { display: flex; align-items: center; gap: 0.85rem; }
        .page-header-title { font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem; }
        
        .header-store-badge {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .header-clock-widget {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8fafc;
            border: 1px solid var(--gray-200);
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--gray-700);
        }
        .header-clock-widget i { color: #10b981; }

        .header-actions { display: flex; align-items: center; gap: 0.65rem; }
        .quick-pos-btn {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);
            transition: all 0.2s ease;
        }
        .quick-pos-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(22, 163, 74, 0.35); color: white; }

        .app-content { padding: 1.25rem 1.5rem; flex: 1; }

        /* Buttons & Forms Utilities */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.55rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: var(--radius-md); border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); color: white; }
        .btn-success { background: #16a34a; color: white; }
        .btn-success:hover { background: #15803d; color: white; }
        .btn-outline { background: white; border-color: var(--gray-300); color: var(--gray-700); }
        .btn-outline:hover { background: var(--gray-50); border-color: var(--gray-400); color: var(--gray-900); }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #b91c1c; color: white; }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.78rem; border-radius: var(--radius-sm); }
        .btn-lg { padding: 0.75rem 1.5rem; font-size: 1rem; border-radius: var(--radius-lg); }

        .card { background: white; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); overflow: hidden; }
        .card-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; background: #fafafa; }
        .card-title { font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700; color: var(--gray-900); display: flex; align-items: center; gap: 0.5rem; }
        .card-body { padding: 1.25rem; }

        .table-responsive { overflow-x: auto; }
        .app-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem; }
        .app-table th { background: #f8fafc; color: var(--gray-600); font-weight: 700; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.05em; padding: 0.85rem 1rem; border-bottom: 1px solid var(--gray-200); }
        .app-table td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--gray-200); color: var(--gray-800); vertical-align: middle; }
        .app-table tbody tr:hover { background: #f8fafc; }

        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem; }
        .form-control { width: 100%; padding: 0.6rem 0.85rem; font-size: 0.9rem; border-radius: var(--radius-md); border: 1px solid var(--gray-300); background: white; color: var(--gray-900); }
        .form-control:focus { outline: none; border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15); }

        .alert { padding: 0.85rem 1.15rem; border-radius: var(--radius-md); font-size: 0.875rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 1rem; }
        .modal-box { background: white; border-radius: var(--radius-xl); max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-xl); }
        .modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; background: #fafafa; }

        .status-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; }

        /* Global Pagination Styles (Green Theme) */
        .pagination, .custom-pagination { display: flex; align-items: center; justify-content: flex-end; list-style: none; margin: 0; padding: 0; }
        .pagination-list { display: flex !important; align-items: center !important; gap: 6px !important; list-style: none !important; margin: 0 !important; padding: 0 !important; flex-wrap: wrap !important; }
        .pagination-item { list-style: none !important; display: inline-flex !important; margin: 0 !important; padding: 0 !important; }
        .pagination-link, .page-item .page-link { 
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important; 
            min-width: 36px !important; 
            height: 36px !important; 
            padding: 0 10px !important; 
            font-size: 0.85rem !important; 
            font-weight: 700 !important; 
            font-family: 'Outfit', sans-serif !important; 
            color: #334155 !important; 
            background: #ffffff !important; 
            border: 1.5px solid #cbd5e1 !important; 
            border-radius: 8px !important; 
            text-decoration: none !important; 
            transition: all 0.15s ease !important; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important; 
            cursor: pointer !important; 
        }
        .pagination-link:hover, .page-item:not(.active):not(.disabled) .page-link:hover { 
            background: #ecfdf5 !important; 
            border-color: #a7f3d0 !important; 
            color: #065f46 !important; 
            transform: translateY(-1px) !important; 
        }
        .pagination-item.active .pagination-link, .page-item.active .page-link { 
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; 
            border-color: #047857 !important; 
            color: #ffffff !important; 
            font-weight: 800 !important; 
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.35) !important; 
        }
        .pagination-item.disabled .pagination-link, .page-item.disabled .page-link { 
            color: #94a3b8 !important; 
            background: #f8fafc !important; 
            border-color: #e2e8f0 !important; 
            cursor: not-allowed !important; 
            opacity: 0.6 !important; 
            box-shadow: none !important; 
            transform: none !important; 
        }
        .pagination-link.dots { 
            border: none !important; 
            background: transparent !important; 
            cursor: default !important; 
            min-width: 20px !important; 
            box-shadow: none !important; 
        }

        nav[role="navigation"] svg, .pagination svg { width: 14px !important; height: 14px !important; max-width: 14px !important; max-height: 14px !important; display: inline-block !important; vertical-align: middle !important; }
        nav[role="navigation"] { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }

        @media (max-width: 900px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); }
            .app-main { margin-left: 0; }
            .header-clock-widget { display: none; }
            .header-store-badge { display: none; }
        }

        /* Global Print Rules */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .app-sidebar,
            .app-header,
            .sidebar-backdrop,
            .no-print,
            .btn,
            button.btn,
            a.btn,
            .timeframe-bar,
            .pagination,
            .toast,
            #receiptPrintFrame {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                display: block !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .app-main {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background: #ffffff !important;
            }
            .app-content {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 0 !important;
            }
            @page {
                margin: 10mm;
            }
        }
    </style>
    @yield('styles')
</head>
<body x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" :class="{ 'active': sidebarOpen }" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="app-sidebar" :class="{ 'open': sidebarOpen }">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard.index') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; text-decoration: none; padding: 0.25rem 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="40 10 520 170" style="height: 52px; width: 100%; max-width: 220px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.4));">
                  <defs>
                    <linearGradient id="appSideLeafGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#4ade80" />
                      <stop offset="60%" stop-color="#22c55e" />
                      <stop offset="100%" stop-color="#16a34a" />
                    </linearGradient>
                    <linearGradient id="appSideGoldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.2" />
                      <stop offset="30%" stop-color="#fbbf24" />
                      <stop offset="50%" stop-color="#fef08a" />
                      <stop offset="70%" stop-color="#fbbf24" />
                      <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.2" />
                    </linearGradient>
                    <filter id="appSideGlow" x="-20%" y="-20%" width="140%" height="140%">
                      <feGaussianBlur stdDeviation="3" result="blur" />
                      <feComposite in="SourceGraphic" in2="blur" operator="over" />
                    </filter>
                  </defs>
                  <path d="M 60,85 Q 300,10 540,85" fill="none" stroke="url(#appSideGoldGrad)" stroke-width="4.5" stroke-linecap="round" />
                  <path d="M 120,83 Q 300,25 480,83" fill="none" stroke="url(#appSideGoldGrad)" stroke-width="2" stroke-linecap="round" opacity="0.85" />
                  <g transform="translate(300, 48)">
                    <path d="M 0,15 C -25,-10 -55,-15 -55,-40 C -55,-48 -35,-52 0,-15 Z" fill="url(#appSideLeafGrad)" />
                    <path d="M 0,15 C -25,-10 -55,-15 -55,-40 C -30,-40 -10,-25 0,15 Z" fill="#15803d" opacity="0.5" />
                    <path d="M 0,15 Q -25,-25 -45,-38" fill="none" stroke="#dcfce7" stroke-width="2" stroke-linecap="round" />
                    <path d="M 0,15 C 25,-10 55,-15 55,-40 C 55,-48 35,-52 0,-15 Z" fill="url(#appSideLeafGrad)" />
                    <path d="M 0,15 C 25,-10 55,-15 55,-40 C 30,-40 10,-25 0,15 Z" fill="#166534" opacity="0.5" />
                    <path d="M 0,15 Q 25,-25 45,-38" fill="none" stroke="#dcfce7" stroke-width="2" stroke-linecap="round" />
                    <circle cx="0" cy="-25" r="4.5" fill="#86efac" filter="url(#appSideGlow)" opacity="0.95" />
                  </g>
                  <text x="300" y="132" font-family="'Outfit', 'Plus Jakarta Sans', Arial Black, sans-serif" font-size="52" font-weight="900" letter-spacing="2.5" fill="#ffffff" text-anchor="middle">HASHMI GRAINS</text>
                  <text x="548" y="105" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#fbbf24">®</text>
                  <line x1="85" y1="162" x2="190" y2="162" stroke="#86efac" stroke-width="2.5" stroke-linecap="round" />
                  <text x="300" y="169" font-family="'Outfit', 'Plus Jakarta Sans', sans-serif" font-size="20" font-weight="800" letter-spacing="5.5" fill="#86efac" text-anchor="middle">PURE BY NATURE</text>
                  <line x1="410" y1="162" x2="515" y2="162" stroke="#86efac" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Billing & Terminal</div>
            <a href="{{ route('pos.index') }}" class="nav-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register"></i>
                <span>POS Terminal</span>
            </a>

            <div class="nav-section-title">Core Management</div>
            <a href="{{ route('dashboard.index') }}" class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard & Reports</span>
            </a>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Products Directory</span>
            </a>
            <a href="{{ route('inventory.low_stock') }}" class="nav-item {{ request()->routeIs('inventory.low_stock') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Low Stock Alerts</span>
                @php
                    $lowStockBadgeCount = \App\Models\Product::where('is_active', true)->whereColumn('stock_quantity', '<=', 'alert_quantity')->count();
                @endphp
                @if($lowStockBadgeCount > 0)
                    <span class="badge">{{ $lowStockBadgeCount }}</span>
                @endif
            </a>
            <a href="{{ route('inventory.logs') }}" class="nav-item {{ request()->routeIs('inventory.logs') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Inventory Logs</span>
            </a>

            <div class="nav-section-title">Financials & Sales</div>
            <a href="{{ route('sales.index') }}" class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Sales History</span>
            </a>
            <a href="{{ route('returns.index') }}" class="nav-item {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-rotate-left"></i>
                <span>Product Returns</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>Cash Drawer & Expense</span>
            </a>

            <div class="nav-section-title">Administration</div>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield"></i>
                <span>Users & Roles</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i>
                <span>Shop Settings</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'Cashier' }}</div>
                <div class="user-role">{{ Auth::user()->role ?? 'Staff' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" title="Logout" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem; padding: 4px;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="app-main">
        <header class="app-header">
            <div class="header-title-section">
                <button class="btn btn-outline btn-sm d-md-none" @click="sidebarOpen = !sidebarOpen">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="page-header-title">
                    @yield('header_title', 'Hashmi Grains POS')
                </div>
                <div class="header-store-badge">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Bahria Town Lahore</span>
                </div>
            </div>

            <!-- Digital Clock & Quick POS -->
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div id="liveClockWidget" class="header-clock-widget">
                    <i class="fa-regular fa-clock"></i>
                    <span id="liveTimeText">--:--:-- --</span>
                </div>

                <div class="header-actions">
                    <a href="{{ route('pos.index') }}" class="quick-pos-btn">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Fast POS</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="app-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:1.1rem;">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:1.1rem;">&times;</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <div>
                        <strong>Please check the following errors:</strong>
                        <ul style="margin-left: 1.25rem; margin-top: 0.25rem;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;font-size:1.1rem;">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <style>
        @media print {
            .app-sidebar, .app-header, .btn, .no-print, .modal-overlay, #receiptPrintFrame, .sidebar-backdrop {
                display: none !important;
            }
            .app-main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background: #ffffff !important;
            }
        }
    </style>

    <script>
        function updateLiveClock() {
            const timeEl = document.getElementById('liveTimeText');
            if (!timeEl) return;
            const now = new Date();
            const options = { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            timeEl.textContent = now.toLocaleDateString('en-US', options);
        }
        updateLiveClock();
        setInterval(updateLiveClock, 1000);

        window.APP_BASE_URL = "{{ url('/') }}";

        window.printReceiptDirect = function(saleId) {
            if (!saleId) return;
            const printUrl = window.APP_BASE_URL + '/sales/' + encodeURIComponent(saleId) + '/receipt?t=' + Date.now();
            let iframe = document.getElementById('receiptPrintFrame');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'receiptPrintFrame';
                iframe.name = 'receiptPrintFrame';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.border = 'none';
                iframe.style.visibility = 'hidden';
                iframe.style.zIndex = '-9999';
                document.body.appendChild(iframe);
            }
            let printed = false;
            iframe.onload = function() {
                try {
                    setTimeout(function() {
                        if (iframe.contentWindow) {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                            printed = true;
                        }
                    }, 300);
                } catch(e) {
                    console.log('Iframe print error fallback to window.open:', e);
                    window.open(printUrl, '_blank');
                }
            };
            iframe.src = printUrl;
        };

        window.printReturnReceiptDirect = function(returnId) {
            if (!returnId) return;
            const printUrl = window.APP_BASE_URL + '/returns/' + encodeURIComponent(returnId) + '/receipt?t=' + Date.now();
            let iframe = document.getElementById('receiptPrintFrame');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'receiptPrintFrame';
                iframe.name = 'receiptPrintFrame';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.border = 'none';
                iframe.style.visibility = 'hidden';
                iframe.style.zIndex = '-9999';
                document.body.appendChild(iframe);
            }
            iframe.onload = function() {
                try {
                    setTimeout(function() {
                        if (iframe.contentWindow) {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        }
                    }, 300);
                } catch(e) {
                    console.log('Iframe return print error fallback:', e);
                    window.open(printUrl, '_blank');
                }
            };
            iframe.src = printUrl;
        };
    </script>
    @yield('scripts')
</body>
</html>
