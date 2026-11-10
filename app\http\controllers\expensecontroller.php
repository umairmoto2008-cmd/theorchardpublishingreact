<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $category = $request->get('category');

        $query = Expense::with('user');

        if ($date) {
            $query->whereDate('expense_date', $date);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $expenses = $query->latest()->paginate(15)->withQueryString();

        // Cash flow summary for selected date
        $selectedDate = Carbon::parse($date);
        $totalExpenses = Expense::whereDate('expense_date', $selectedDate)->sum('amount');
        $cashSales = Sale::whereDate('created_at', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->where('payment_method', 'cash')
            ->sum('grand_total');
        $cardSales = Sale::whereDate('created_at', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->where('payment_method', '!=', 'cash')
            ->sum('grand_total');
        $cashRefunds = \App\Models\ProductReturn::whereDate('created_at', $selectedDate)->sum('total_refund');
        
        $netCashBalance = $cashSales - $totalExpenses - $cashRefunds;

        return view('expenses.index', compact('expenses', 'date', 'category', 'totalExpenses', 'cashSales', 'cardSales', 'cashRefunds', 'netCashBalance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string|max:50',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return back()->with('success', 'Expense recorded successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted successfully!');
    }
}
