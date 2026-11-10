@extends('layouts.app')

@section('title', 'Cash Drawer & Expenses - Hashmi Grains')
@section('header_title', 'Cash Drawer & Expense Ledger')

@section('content')
<div x-data="{ showAddExpenseModal: false }">
    <!-- Top Action & Date Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="{{ route('expenses.index') }}" style="display: flex; align-items: center; gap: 0.75rem;">
            <label class="form-label" style="margin: 0; white-space: nowrap;">Select Date:</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control" style="width: auto;" onchange="this.form.submit()">
        </form>

        <button @click="showAddExpenseModal = true" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Record Expense / Cash Out
        </button>
    </div>

    <!-- Cash Drawer Summary Grid for Selected Date -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <!-- Cash Sales -->
        <div class="card" style="padding: 1.25rem; border-left: 4px solid #16a34a;">
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Total Cash Sales</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #16a34a; margin-top: 4px;">
                Rs. {{ number_format($cashSales, 0) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">Digital / Card Sales: Rs. {{ number_format($cardSales, 0) }}</div>
        </div>

        <!-- Total Expenses -->
        <div class="card" style="padding: 1.25rem; border-left: 4px solid #dc2626;">
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Total Expenses (Cash Out)</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 4px;">
                Rs. {{ number_format($totalExpenses, 0) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">{{ $expenses->total() }} Expense entries</div>
        </div>

        @if($cashRefunds > 0)
        <!-- Cash Refunds -->
        <div class="card" style="padding: 1.25rem; border-left: 4px solid #ea580c;">
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--gray-500); text-transform: uppercase;">Customer Refunds (Cash Out)</div>
            <div style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; color: #ea580c; margin-top: 4px;">
                Rs. {{ number_format($cashRefunds, 0) }}
            </div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">Returned Items Refunded</div>
        </div>
        @endif

        <!-- Net In-Drawer Cash -->
        <div class="card" style="padding: 1.25rem; border-left: 4px solid #0f5132; background: #f0fdf4;">
            <div style="font-size: 0.78rem; font-weight: 800; color: #065f46; text-transform: uppercase;">Net Cash in Drawer</div>
            <div style="font-family: 'Outfit'; font-size: 1.65rem; font-weight: 800; color: #0f5132; margin-top: 4px;">
                Rs. {{ number_format($netCashBalance, 0) }}
            </div>
            <div style="font-size: 0.75rem; color: #047857; margin-top: 4px;">Sales minus Expenses minus Refunds</div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-receipt" style="color: #dc2626;"></i>
                <span>Daily Expense Entries ({{ \Carbon\Carbon::parse($date)->format('d M Y') }})</span>
            </h3>
        </div>

        <div class="table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Expense Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Logged By</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $exp->created_at->format('h:i A') }}
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900);">{{ $exp->title }}</div>
                            </td>
                            <td>
                                <span class="status-badge badge-warning" style="text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $exp->category) }}
                                </span>
                            </td>
                            <td style="font-family: 'Outfit'; font-weight: 800; font-size: 1rem; color: #dc2626;">
                                Rs. {{ number_format($exp->amount, 0) }}
                            </td>
                            <td style="font-size: 0.8rem; color: var(--gray-600);">
                                {{ $exp->user->name ?? 'Staff' }}
                            </td>
                            <td style="font-size: 0.82rem; color: var(--gray-600);">
                                {{ $exp->notes ?: '-' }}
                            </td>
                            <td>
                                <form action="{{ route('expenses.destroy', $exp) }}" method="POST" onsubmit="return confirm('Delete this expense?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444; border:none; padding: 4px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                                <i class="fa-solid fa-wallet" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                <p style="font-weight: 600;">No expenses recorded for this date</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div style="padding: 1rem; border-top: 1px solid var(--gray-200);">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Add Expense -->
    <div class="modal-overlay" x-show="showAddExpenseModal" style="display: none;">
        <div class="modal-box" @click.away="showAddExpenseModal = false">
            <div class="modal-header" style="background: #fff1f2;">
                <h3 class="card-title">
                    <i class="fa-solid fa-minus-circle" style="color: #dc2626;"></i>
                    <span>Record Cash Expense / Outward</span>
                </h3>
                <button @click="showAddExpenseModal = false" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Expense Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Tea & Refreshments, Packaging Bags, Fuel" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="packaging">Packaging & Bags</option>
                                <option value="refreshment">Tea & Refreshment</option>
                                <option value="transport">Delivery & Transport</option>
                                <option value="utility">Electricity & Utilities</option>
                                <option value="salary">Staff Daily Wages</option>
                                <option value="maintenance">Maintenance & Repair</option>
                                <option value="rent">Shop Rent</option>
                                <option value="other">Other Expense</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Amount (Rs) *</label>
                            <input type="number" step="any" min="1" name="amount" class="form-control" placeholder="e.g. 500" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Expense Date *</label>
                        <input type="date" name="expense_date" value="{{ $date }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="showAddExpenseModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-danger">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
