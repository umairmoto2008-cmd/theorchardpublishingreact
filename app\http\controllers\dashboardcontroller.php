<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Expense;
use App\Models\ProductReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function calculateMetrics($startDate, $endDate, $isAllTime = false)
    {
        $salesQuery = Sale::where('status', '!=', 'cancelled');
        $expenseQuery = Expense::query();
        $returnQuery = ProductReturn::query();

        if (!$isAllTime && $startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $expenseQuery->whereBetween('expense_date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);
            $returnQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $revenue = (float) $salesQuery->sum('grand_total');
        $ordersCount = (int) $salesQuery->count();
        $grossProfit = (float) $salesQuery->sum('profit');
        $totalCost = (float) $salesQuery->sum('total_cost');
        $discountsGiven = (float) $salesQuery->sum('discount');

        $expenses = (float) $expenseQuery->sum('amount');
        $returnsRefund = (float) $returnQuery->sum('total_refund');
        $returnsCount = (int) $returnQuery->count();

        $cogs = $totalCost > 0 ? $totalCost : ($revenue - $grossProfit);
        $netProfit = $grossProfit - $expenses;
        $grossMarginPct = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0;
        $netMarginPct = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;
        $avgOrder = $ordersCount > 0 ? round($revenue / $ordersCount, 0) : 0;

        // Cash Drawer reconciliation (Cash In minus Operating Expenses minus Cash Refunds Given)
        $cashQuery = clone $salesQuery;
        $cashSales = (float) $cashQuery->where('payment_method', 'cash')->sum('grand_total');
        $digitalSales = $revenue - $cashSales;
        $cashDrawer = $cashSales - $expenses - $returnsRefund;

        return [
            'revenue' => $revenue,
            'orders_count' => $ordersCount,
            'gross_profit' => $grossProfit,
            'cogs' => $cogs,
            'discounts' => $discountsGiven,
            'expenses' => $expenses,
            'returns_refund' => $returnsRefund,
            'returns_count' => $returnsCount,
            'net_profit' => $netProfit,
            'gross_margin_pct' => $grossMarginPct,
            'net_margin_pct' => $netMarginPct,
            'avg_order' => $avgOrder,
            'cash_sales' => $cashSales,
            'digital_sales' => $digitalSales,
            'cash_drawer' => $cashDrawer,
        ];
    }

    public function index(Request $request)
    {
        // 1. Timeframe Definitions
        $now = Carbon::now();
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $yesterdayStart = Carbon::yesterday();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisYearStart = Carbon::now()->startOfYear();
        $thisYearEnd = Carbon::now()->endOfYear();

        // Calculate all 7 timeframes
        $todayMetrics = $this->calculateMetrics($todayStart, $todayEnd);
        $yesterdayMetrics = $this->calculateMetrics($yesterdayStart, $yesterdayEnd);
        $weekMetrics = $this->calculateMetrics($weekStart, $weekEnd);
        $monthMetrics = $this->calculateMetrics($monthStart, $monthEnd);
        $lastMonthMetrics = $this->calculateMetrics($lastMonthStart, $lastMonthEnd);
        $thisYearMetrics = $this->calculateMetrics($thisYearStart, $thisYearEnd);
        $allTimeMetrics = $this->calculateMetrics(null, null, true);

        // Calculate Growth Rates
        $dailyRevenueGrowth = $yesterdayMetrics['revenue'] > 0
            ? round((($todayMetrics['revenue'] - $yesterdayMetrics['revenue']) / $yesterdayMetrics['revenue']) * 100, 1)
            : ($todayMetrics['revenue'] > 0 ? 100 : 0);

        $dailyProfitGrowth = abs($yesterdayMetrics['net_profit']) > 0
            ? round((($todayMetrics['net_profit'] - $yesterdayMetrics['net_profit']) / abs($yesterdayMetrics['net_profit'])) * 100, 1)
            : ($todayMetrics['net_profit'] > 0 ? 100 : 0);

        $monthlyRevenueGrowth = $lastMonthMetrics['revenue'] > 0
            ? round((($monthMetrics['revenue'] - $lastMonthMetrics['revenue']) / $lastMonthMetrics['revenue']) * 100, 1)
            : ($monthMetrics['revenue'] > 0 ? 100 : 0);

        $monthlyProfitGrowth = abs($lastMonthMetrics['net_profit']) > 0
            ? round((($monthMetrics['net_profit'] - $lastMonthMetrics['net_profit']) / abs($lastMonthMetrics['net_profit'])) * 100, 1)
            : ($monthMetrics['net_profit'] > 0 ? 100 : 0);

        // 3. Payment Methods Breakdown (This Month)
        $paymentMethodsMonth = Sale::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->select('payment_method', DB::raw('SUM(grand_total) as total_amount'), DB::raw('COUNT(*) as total_count'))
            ->groupBy('payment_method')
            ->get();

        // 4. Expense Breakdown by Category (This Month)
        $expenseCategoriesMonth = Expense::whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->select('category', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // 5. Inventory Health & Valuation
        $totalProducts = Product::where('is_active', true)->count();
        $outOfStockCount = Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count();
        $lowStockCount = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->count();

        $stockValuationCost = (float) (Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * purchase_price) as total_val')
            ->value('total_val') ?? 0);

        $stockValuationSale = (float) (Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * sale_price) as total_val')
            ->value('total_val') ?? 0);

        $potentialStockProfit = $stockValuationSale - $stockValuationCost;
        $potentialStockMarginPct = $stockValuationCost > 0 ? round(($potentialStockProfit / $stockValuationCost) * 100, 1) : 0;

        // 6. Top 6 Profit-Generating Products (All Time / This Month)
        $topProfitProducts = SaleItem::select(
                'product_name',
                'unit',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('product_name', 'unit')
            ->orderByDesc('total_profit')
            ->take(6)
            ->get();

        // 7. Top 6 Fastest Volume Selling Products
        $topVolumeProducts = SaleItem::select(
                'product_name',
                'unit',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->groupBy('product_name', 'unit')
            ->orderByDesc('total_qty')
            ->take(6)
            ->get();

        // 8. Recent 8 Sales
        $recentSales = Sale::with(['items', 'user'])
            ->latest()
            ->take(8)
            ->get();

        // 9. Recent 5 Returns
        $recentReturns = ProductReturn::with(['sale', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // 10. 14-Day Performance Trend for Chart
        $chartLabels = [];
        $chartSales = [];
        $chartGrossProfit = [];
        $chartExpenses = [];
        $chartNetProfit = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M (D)');
            $salesVal = (float) Sale::whereDate('created_at', $date)->where('status', '!=', 'cancelled')->sum('grand_total');
            $grossProfitVal = (float) Sale::whereDate('created_at', $date)->where('status', '!=', 'cancelled')->sum('profit');
            $expensesVal = (float) Expense::whereDate('expense_date', $date)->sum('amount');
            $netProfitVal = $grossProfitVal - $expensesVal;

            $chartSales[] = $salesVal;
            $chartGrossProfit[] = $grossProfitVal;
            $chartExpenses[] = $expensesVal;
            $chartNetProfit[] = $netProfitVal;
        }

        // Summary metrics dictionary for Alpine.js timeframe switcher
        $timeframesData = [
            'today' => array_merge($todayMetrics, [
                'title' => 'Today (Aaj)',
                'subtitle' => Carbon::today()->format('d M Y, l'),
                'revenue_growth' => $dailyRevenueGrowth,
                'profit_growth' => $dailyProfitGrowth,
                'growth_label' => 'vs Yesterday'
            ]),
            'yesterday' => array_merge($yesterdayMetrics, [
                'title' => 'Yesterday (Kal)',
                'subtitle' => Carbon::yesterday()->format('d M Y, l'),
                'revenue_growth' => 0,
                'profit_growth' => 0,
                'growth_label' => 'Previous Day'
            ]),
            'week' => array_merge($weekMetrics, [
                'title' => 'This Week (Is Hafte)',
                'subtitle' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M Y'),
                'revenue_growth' => 0,
                'profit_growth' => 0,
                'growth_label' => 'Current Week'
            ]),
            'month' => array_merge($monthMetrics, [
                'title' => 'This Month (Is Maah)',
                'subtitle' => Carbon::now()->format('F Y'),
                'revenue_growth' => $monthlyRevenueGrowth,
                'profit_growth' => $monthlyProfitGrowth,
                'growth_label' => 'vs Last Month'
            ]),
            'last_month' => array_merge($lastMonthMetrics, [
                'title' => 'Last Month (Pichla Maah)',
                'subtitle' => Carbon::now()->subMonth()->format('F Y'),
                'revenue_growth' => 0,
                'profit_growth' => 0,
                'growth_label' => 'Previous Month'
            ]),
            'this_year' => array_merge($thisYearMetrics, [
                'title' => 'This Year (' . Carbon::now()->year . ')',
                'subtitle' => Carbon::now()->format('Y') . ' (' . $thisYearStart->format('d M') . ' – ' . $thisYearEnd->format('d M Y') . ')',
                'revenue_growth' => 0,
                'profit_growth' => 0,
                'growth_label' => 'Current Year'
            ]),
            'all_time' => array_merge($allTimeMetrics, [
                'title' => 'All-Time Lifetime',
                'subtitle' => 'Entire store history',
                'revenue_growth' => 0,
                'profit_growth' => 0,
                'growth_label' => 'Lifetime Total'
            ]),
        ];

        // Available years for custom selector dropdown (Strictly 2026 to 2028)
        $allYears = [2026, 2027, 2028];

        return view('dashboard.index', compact(
            'todayMetrics',
            'yesterdayMetrics',
            'weekMetrics',
            'monthMetrics',
            'lastMonthMetrics',
            'thisYearMetrics',
            'allTimeMetrics',
            'allYears',
            'dailyRevenueGrowth',
            'dailyProfitGrowth',
            'monthlyRevenueGrowth',
            'monthlyProfitGrowth',
            'timeframesData',
            'paymentMethodsMonth',
            'expenseCategoriesMonth',
            'totalProducts',
            'outOfStockCount',
            'lowStockCount',
            'stockValuationCost',
            'stockValuationSale',
            'potentialStockProfit',
            'potentialStockMarginPct',
            'topProfitProducts',
            'topVolumeProducts',
            'recentSales',
            'recentReturns',
            'chartLabels',
            'chartSales',
            'chartGrossProfit',
            'chartExpenses',
            'chartNetProfit'
        ));
    }

    /**
     * AJAX endpoint to fetch dynamic custom Month & Year metrics
     */
    public function getCustomPeriodStats(Request $request)
    {
        $year = (int) ($request->input('year') ?: Carbon::now()->year);
        $month = $request->input('month');

        if ($month === 'all' || empty($month)) {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
            $title = "Year {$year}";
            $subtitle = "01 Jan {$year} – 31 Dec {$year} (Full Year Summary)";
        } else {
            $monthNum = max(1, min(12, (int) $month));
            $startDate = Carbon::createFromDate($year, $monthNum, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $monthNum, 1)->endOfMonth();
            $monthName = $startDate->format('F');
            $title = "{$monthName} {$year}";
            $subtitle = $startDate->format('d M Y') . ' – ' . $endDate->format('d M Y') . ' (Monthly Summary)';
        }

        $metrics = $this->calculateMetrics($startDate, $endDate, false);

        $response = array_merge($metrics, [
            'title' => $title,
            'subtitle' => $subtitle,
            'revenue_growth' => 0,
            'profit_growth' => 0,
            'growth_label' => 'Custom Period',
            'selected_year' => $year,
            'selected_month' => $month
        ]);

        return response()->json($response);
    }
}


