<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Fetch real Stats excluding cancelled orders
        $totalRevenue = (float) \App\Models\Order::where('status', '!=', 'cancelled')->sum('final_amount');
        $recoveredDues = (float) \App\Models\Payment::sum('amount');
        $outstandingDues = (float) \App\Models\Order::where('status', '!=', 'cancelled')->sum('remaining_amount');
        $totalExpenses = (float) \App\Models\Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses; // sales minus overhead expenses
        
        $activeCustomersCount = \App\Models\Customer::count();
        $pendingOrdersCount = \App\Models\Order::where('status', '!=', 'cancelled')->where('remaining_amount', '>', 0)->count();

        // 2. Fetch Low Stock warnings (stock <= 15)
        $lowStockProducts = \App\Models\Product::where('current_stock', '<=', 15)
            ->orderBy('current_stock', 'asc')
            ->take(5)
            ->get();

        // 3. Fetch recent payments/orders for activity log (excluding cancelled orders)
        $recentOrders = \App\Models\Order::with('customer')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($order) {
                return [
                    'name' => $order->customer->name ?? 'Guest Customer',
                    'event' => 'Created Order #' . $order->id . ' worth PKR ' . number_format($order->final_amount),
                    'status' => $order->remaining_amount > 0 ? 'Pending' : 'Completed',
                    'time' => $order->created_at->diffForHumans()
                ];
            });

        $recentPayments = \App\Models\Payment::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($payment) {
                // Find order reference or customer details
                $customer = \App\Models\Customer::where('user_id', $payment->user_id)->first();
                return [
                    'name' => $customer->name ?? 'Customer ID: ' . $payment->user_id,
                    'event' => 'Recorded payment of PKR ' . number_format($payment->amount) . ' via ' . ($payment->payment_method ?? 'Cash'),
                    'status' => 'Completed',
                    'time' => $payment->created_at->diffForHumans()
                ];
            });

        // Merge and sort activities by time
        $activities = $recentOrders->concat($recentPayments)->take(6);

        // 4. Generate Monthly bar-chart heights (Sales per month for the last 12 months)
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $sales = \App\Models\Order::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('final_amount');
            $monthlySales[] = [
                'name' => $month->format('M'),
                'amount' => (float) $sales
            ];
        }

        // Get max sales to scale the heights
        $maxSales = collect($monthlySales)->max('amount') ?: 1000;

        return view('admin.dashboard', compact(
            'totalRevenue',
            'recoveredDues',
            'outstandingDues',
            'totalExpenses',
            'netProfit',
            'activeCustomersCount',
            'pendingOrdersCount',
            'lowStockProducts',
            'activities',
            'monthlySales',
            'maxSales'
        ));
    }
}
