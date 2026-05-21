<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function ledger(Customer $customer)
    {

        // Fetch all orders associated with the customer's user_id
        $orders = Order::where('user_id', $customer->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all payments associated with the customer's user_id
        $payments = Payment::where('user_id', $customer->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics, excluding cancelled orders
        $activeOrders = $orders->where('status', '!=', 'cancelled');
        $totalVolume = (float) $activeOrders->sum('final_amount');
        $totalSavings = (float) $activeOrders->sum('discount');
        $outstandingBalance = (float) $activeOrders->sum('remaining_amount');
        
        // Total paid includes paid amount on orders + manual payments not linked to orders (to prevent double counting when order payments update order's paid_amount)
        $totalPaid = (float) ($activeOrders->sum('paid_amount') + $payments->whereNull('order_id')->sum('amount'));

        return view('admin.customer-ledger', compact(
            'customer',
            'orders',
            'payments',
            'totalVolume',
            'totalSavings',
            'outstandingBalance',
            'totalPaid'
        ));
    }

    public function index($type = null)
    {
        $query = Customer::query();

        // Standardize customer types
        if ($type === 'customer') {
            $query->where('customer_type', 'customer');
            $title = 'Retail Customers';
        } elseif ($type === 'outside') {
            $query->where('customer_type', 'outside');
            $title = 'Outside Customers';
        } elseif ($type === 'distributor') {
            $query->where('customer_type', 'distributor');
            $title = 'Distributor Customers';
        } else {
            $title = 'All Customers';
        }

        // Search functionality
        if (request()->has('search') && request()->get('search') !== '') {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->get();

        return view('admin.customers.list', compact('customers', 'title', 'type'));
    }

    public function dues()
    {
        // Fetch all customers who have a total remaining balance > 0 in their non-cancelled orders
        $customers = Customer::select('customers.*')
            ->selectSub(function ($query) {
                $query->from('orders')
                    ->whereColumn('orders.user_id', 'customers.user_id')
                    ->where('status', '!=', 'cancelled')
                    ->selectRaw('SUM(remaining_amount)');
            }, 'total_dues')
            ->selectSub(function ($query) {
                $query->from('orders')
                    ->whereColumn('orders.user_id', 'customers.user_id')
                    ->where('status', '!=', 'cancelled')
                    ->selectRaw('COUNT(*)');
            }, 'total_orders')
            ->selectSub(function ($query) {
                $query->from('orders')
                    ->whereColumn('orders.user_id', 'customers.user_id')
                    ->where('status', '!=', 'cancelled')
                    ->where('remaining_amount', '>', 0)
                    ->selectRaw('COUNT(*)');
            }, 'pending_orders')
            ->having('total_dues', '>', 0)
            ->orderBy('total_dues', 'desc')
            ->get();

        return view('admin.customers.dues', compact('customers'));
    }

    public function recordCustomerDuesPayment(Request $request, Customer $customer)
    {
        // Calculate current total outstanding dues excluding cancelled orders
        $totalDues = Order::where('user_id', $customer->user_id)->where('status', '!=', 'cancelled')->sum('remaining_amount');

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $totalDues],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $remainingPayment = (float) $data['amount'];

        DB::transaction(function () use ($customer, $data, &$remainingPayment) {
            // Fetch pending non-cancelled orders, oldest first
            $pendingOrders = Order::where('user_id', $customer->user_id)
                ->where('status', '!=', 'cancelled')
                ->where('remaining_amount', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($pendingOrders as $order) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $orderRemaining = (float) $order->remaining_amount;
                $allocatedAmount = min($remainingPayment, $orderRemaining);

                // Create payment record for this order
                Payment::create([
                    'user_id' => $customer->user_id,
                    'order_id' => $order->id,
                    'amount' => $allocatedAmount,
                    'payment_date' => $data['payment_date'],
                    'payment_method' => $data['payment_method'],
                    'notes' => ($data['notes'] ?? '') . " (Central dues recovery)",
                ]);

                // Update order amounts
                $newPaid = (float) $order->paid_amount + $allocatedAmount;
                $newRemaining = max(0, $orderRemaining - $allocatedAmount);

                $order->update([
                    'paid_amount' => $newPaid,
                    'remaining_amount' => $newRemaining,
                ]);

                $remainingPayment -= $allocatedAmount;
            }
        });

        return redirect()->back()->with('success', 'Outstanding balance recovered and allocated to pending orders.');
    }
}



