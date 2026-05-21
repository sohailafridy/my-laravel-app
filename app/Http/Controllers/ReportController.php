<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\ExpenseType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default to monthly only if interval is not present at all in request (first load)
        $interval = $request->has('interval') ? $request->input('interval') : 'monthly';
        $metric = $request->input('metric', 'all');
        $search = $request->input('search');
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Override custom dates if interval is selected
        if (!empty($interval)) {
            if ($interval === 'daily') {
                // $startDate = Carbon::now()->subDays(30)->toDateString();
                // $endDate = Carbon::now()->toDateString();

                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->toDateString();

            } else {
                $startDate = Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::now()->toDateString();
            }
        } else {
            // No interval selected (Custom Date Range) -> Default dates if completely empty
            if (empty($startDate)) {
                $startDate = Carbon::now()->subDays(30)->toDateString();
            }
            if (empty($endDate)) {
                $endDate = Carbon::now()->toDateString();
            }
        }

        $groupInterval = !empty($interval) ? $interval : 'daily';

        // 1. Sales Query Grouped
        $salesQuery = Order::where('status', '!=', 'cancelled');
        if ($startDate) {
            $salesQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('created_at', '<=', $endDate);
        }
        if (!empty($search)) {
            $salesQuery->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        switch ($groupInterval) {
            case 'daily':
                $salesQuery->selectRaw('DATE(created_at) as period, SUM(final_amount) as sales, COUNT(*) as orders_count');
                break;
            case 'weekly':
                $salesQuery->selectRaw('DATE_FORMAT(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), "%Y-%m-%d") as period, SUM(final_amount) as sales, COUNT(*) as orders_count');
                break;
            case 'yearly':
                $salesQuery->selectRaw('YEAR(created_at) as period, SUM(final_amount) as sales, COUNT(*) as orders_count');
                break;
            case 'monthly':
            default:
                $salesQuery->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, SUM(final_amount) as sales, COUNT(*) as orders_count');
                break;
        }
        $salesData = $salesQuery->groupBy('period')->get()->keyBy('period');

        // 2. Expenses Query Grouped
        $expenseQuery = Expense::query()
            ->join('expense_type', 'expenses.expense_type_id', '=', 'expense_type.exp_type_id');
        if ($startDate) {
            $expenseQuery->whereDate('expenses.date', '>=', $startDate);
        }
        if ($endDate) {
            $expenseQuery->whereDate('expenses.date', '<=', $endDate);
        }
        if (!empty($search)) {
            $expenseQuery->where(function($q) use ($search) {
                $q->where('expenses.detail', 'like', "%{$search}%")
                  ->orWhere('expense_type.expense_type', 'like', "%{$search}%");
            });
        }

        switch ($groupInterval) {
            case 'daily':
                $expenseQuery->selectRaw('expenses.date as period, SUM(expenses.amount) as expenses, COUNT(*) as expenses_count');
                break;
            case 'weekly':
                $expenseQuery->selectRaw('DATE_FORMAT(DATE_SUB(expenses.date, INTERVAL WEEKDAY(expenses.date) DAY), "%Y-%m-%d") as period, SUM(expenses.amount) as expenses, COUNT(*) as expenses_count');
                break;
            case 'yearly':
                $expenseQuery->selectRaw('YEAR(expenses.date) as period, SUM(expenses.amount) as expenses, COUNT(*) as expenses_count');
                break;
            case 'monthly':
            default:
                $expenseQuery->selectRaw('DATE_FORMAT(expenses.date, "%Y-%m") as period, SUM(expenses.amount) as expenses, COUNT(*) as expenses_count');
                break;
        }
        $expenseData = $expenseQuery->groupBy('period')->get()->keyBy('period');

        // 3. Profit Grouped (COGS)
        $profitQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', '!=', 'cancelled');
        if ($startDate) {
            $profitQuery->whereDate('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $profitQuery->whereDate('orders.created_at', '<=', $endDate);
        }
        if (!empty($search)) {
            $profitQuery->where(function($q) use ($search) {
                $q->where('orders.id', 'like', "%{$search}%")
                  ->orWhere('products.name', 'like', "%{$search}%")
                  ->orWhereHas('order.customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        switch ($groupInterval) {
            case 'daily':
                $profitQuery->selectRaw('DATE(orders.created_at) as period, SUM(order_items.final_price) as item_revenue, SUM(order_items.cost_price * order_items.quantity) as item_cost');
                break;
            case 'weekly':
                $profitQuery->selectRaw('DATE_FORMAT(DATE_SUB(orders.created_at, INTERVAL WEEKDAY(orders.created_at) DAY), "%Y-%m-%d") as period, SUM(order_items.final_price) as item_revenue, SUM(order_items.cost_price * order_items.quantity) as item_cost');
                break;
            case 'yearly':
                $profitQuery->selectRaw('YEAR(orders.created_at) as period, SUM(order_items.final_price) as item_revenue, SUM(order_items.cost_price * order_items.quantity) as item_cost');
                break;
            case 'monthly':
            default:
                $profitQuery->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m") as period, SUM(order_items.final_price) as item_revenue, SUM(order_items.cost_price * order_items.quantity) as item_cost');
                break;
        }
        $profitData = $profitQuery->groupBy('period')->get()->keyBy('period');

        // Combine all periods chronologically
        $allPeriods = collect([])
            ->concat($salesData->keys())
            ->concat($expenseData->keys())
            ->concat($profitData->keys())
            ->unique()
            ->sort()
            ->reverse(); // Newest first

        $reportRows = [];
        $totalSales = 0.0;
        $totalExpenses = 0.0;
        $totalCogs = 0.0;
        $totalOrdersCount = 0;
        $totalExpensesCount = 0;

        foreach ($allPeriods as $period) {
            $sales = isset($salesData[$period]) ? (float)$salesData[$period]->sales : 0.0;
            $ordersCount = isset($salesData[$period]) ? (int)$salesData[$period]->orders_count : 0;
            
            $expenses = isset($expenseData[$period]) ? (float)$expenseData[$period]->expenses : 0.0;
            $expensesCount = isset($expenseData[$period]) ? (int)$expenseData[$period]->expenses_count : 0;
            
            $cogs = isset($profitData[$period]) ? (float)$profitData[$period]->item_cost : 0.0;
            $grossProfit = $sales - $cogs;
            $netProfit = $grossProfit - $expenses;

            $totalSales += $sales;
            $totalExpenses += $expenses;
            $totalCogs += $cogs;
            $totalOrdersCount += $ordersCount;
            $totalExpensesCount += $expensesCount;

            $reportRows[] = (object)[
                'period' => $period,
                'sales' => $sales,
                'orders_count' => $ordersCount,
                'expenses' => $expenses,
                'expenses_count' => $expensesCount,
                'cogs' => $cogs,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
            ];
        }

        $totalGrossProfit = $totalSales - $totalCogs;
        $totalNetProfit = $totalGrossProfit - $totalExpenses;

        // 4. Fetch granular lists if specific metric or all is selected
        $ordersDetail = collect();
        if ($metric === 'all' || $metric === 'sales' || $metric === 'profit') {
            $ordersDetailQuery = Order::with(['customer', 'items.product'])->where('status', '!=', 'cancelled');
            if ($startDate) {
                $ordersDetailQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $ordersDetailQuery->whereDate('created_at', '<=', $endDate);
            }
            if (!empty($search)) {
                $ordersDetailQuery->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            $ordersDetail = $ordersDetailQuery->orderBy('id', 'desc')->take(100)->get();
        }

        $expensesDetail = collect();
        if ($metric === 'all' || $metric === 'expense') {
            $expensesDetailQuery = Expense::select('expenses.*', 'expense_type.expense_type as category_name')
                ->join('expense_type', 'expenses.expense_type_id', '=', 'expense_type.exp_type_id');
            if ($startDate) {
                $expensesDetailQuery->whereDate('expenses.date', '>=', $startDate);
            }
            if ($endDate) {
                $expensesDetailQuery->whereDate('expenses.date', '<=', $endDate);
            }
            if (!empty($search)) {
                $expensesDetailQuery->where(function($q) use ($search) {
                    $q->where('expenses.detail', 'like', "%{$search}%")
                      ->orWhere('expense_type.expense_type', 'like', "%{$search}%");
                });
            }
            $expensesDetail = $expensesDetailQuery->orderBy('expenses.date', 'desc')->take(100)->get();
        }

        return view('admin.reports.index', compact(
            'reportRows',
            'totalSales',
            'totalExpenses',
            'totalCogs',
            'totalGrossProfit',
            'totalNetProfit',
            'totalOrdersCount',
            'totalExpensesCount',
            'ordersDetail',
            'expensesDetail',
            'interval',
            'metric',
            'search',
            'startDate',
            'endDate'
        ));
    }
}
