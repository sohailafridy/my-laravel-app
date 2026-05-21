<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\ProductSummary;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function create()
    {
        $customers = Customer::orderBy('name')
            ->get(['id', 'name', 'phone', 'customer_type']);

        $products = Product::orderBy('name')
            ->get(['id', 'name', 'sku', 'sale_price', 'current_stock']);

        return view('admin.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // exit;
        $data = $request->validate([
            'outside_customer' => ['nullable', 'boolean'],
            'customer_id' => ['nullable', 'required_without:outside_customer', 'exists:customers,id'],
            'outside_customer_name' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:100'],
            'outside_customer_phone' => ['nullable', 'string', 'max:20'],
            'outside_customer_cnic' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:20'],
            'outside_customer_email' => ['nullable', 'required_if:outside_customer,1', 'email', 'max:20'],
            'outside_customer_from_add' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:255'],
            'outside_customer_to_add' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:255'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $orderId = DB::transaction(function () use ($data) {
            $products = Product::lockForUpdate()
                ->whereIn('id', collect($data['items'])->pluck('product_id')->all())
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $preparedItems = [];
            $requestedQuantities = collect($data['items'])
                ->groupBy('product_id')
                ->map(fn ($items) => $items->sum(fn ($item) => (int) $item['quantity']));

            foreach ($requestedQuantities as $productId => $quantity) {
                $product = $products[(int) $productId];
                $stock = (int) ($product->current_stock ?? 0);

                if ($stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}.",
                    ]);
                }
            }

            foreach ($data['items'] as $item) {
                $product = $products[(int) $item['product_id']];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($product->sale_price ?? 0);
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];
            }

            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $orderDiscount = (float) ($data['discount'] ?? 0);
            $totalDiscount = min($subtotal, ($subtotal * ($discountPercent / 100)) + $orderDiscount);
            $finalAmount = max(0, $subtotal - $totalDiscount);
            $paidAmount = min((float) ($data['paid_amount'] ?? 0), $finalAmount);
            $remainingAmount = max(0, $finalAmount - $paidAmount);
            $userId = null;

            if (empty($data['outside_customer']) && ! empty($data['customer_id'])) {
                $userId = Customer::find($data['customer_id'])?->user_id;
            }

            if (! empty($data['outside_customer'])) {
                $customer = Customer::query()
                    ->where('cnic', $data['outside_customer_cnic'])
                    ->orWhere('email', $data['outside_customer_email'])
                    ->first();
                if ($customer) {
                    $userId = $customer->user_id;
                } else {
                    $user = new User();
                    $user->name = $data['outside_customer_name'];
                    $user->user_type = 'outside_customer';
                    $user->email = $data['outside_customer_email'];
                    $user->password = bcrypt('password');
                    $user->save();
                    $userId = $user->id;

                    $customerIns = DB::table('customers')->insertGetId([
                        'user_id' => $userId,
                        'name' => $data['outside_customer_name'],
                        'email' => $data['outside_customer_email'],
                        'phone' => $data['outside_customer_phone'] ?? null,
                        'cnic' => $data['outside_customer_cnic'],
                        'customer_type' => 'outside',
                        'from_add' => $data['outside_customer_from_add'],
                        'to_add' => $data['outside_customer_to_add'],
                        'created_at' => now(),
                    ]);
                }
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $subtotal,
                'discount' => $totalDiscount,
                'discount_percent' => (int) round($discountPercent),
                'final_amount' => $finalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'remaining_amount_fixed' => $remainingAmount,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $lineDiscount = $subtotal > 0
                    ? ($preparedItem['total_price'] / $subtotal) * $totalDiscount
                    : 0;
                $lineFinalPrice = max(0, $preparedItem['total_price'] - $lineDiscount);
                $lineCost = ((float) ($preparedItem['product']->purchase_price ?? 0)) * $preparedItem['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $preparedItem['product']->id,
                    'quantity' => $preparedItem['quantity'],
                    'unit_price' => $preparedItem['unit_price'],
                    'total_price' => $preparedItem['total_price'],
                    'discount' => $lineDiscount,
                    'final_price' => $lineFinalPrice,
                    'cost_price' => $preparedItem['product']->purchase_price,
                ]);

                $preparedItem['product']->current_stock = (int) $preparedItem['product']->current_stock - $preparedItem['quantity'];
                $preparedItem['product']->save();

                StockMovement::create([
                    'product_id' => $preparedItem['product']->id,
                    'type' => 'out',
                    'quantity' => $preparedItem['quantity'],
                    'reference_id' => $order->id,
                ]);

                $summary = ProductSummary::firstOrNew(['product_id' => $preparedItem['product']->id]);
                $summary->total_sold = ((int) $summary->total_sold) + $preparedItem['quantity'];
                $summary->total_revenue = ((float) $summary->total_revenue) + $lineFinalPrice;
                $summary->total_profit = ((float) $summary->total_profit) + ($lineFinalPrice - $lineCost);
                $summary->current_stock = $preparedItem['product']->current_stock;
                $summary->last_sale_date = now()->toDateString();
                $summary->save();
            }

            return $order->id;
        });

        return redirect()
            ->route('order.invoice', $orderId)
            ->with('success', 'Order completed successfully.');
    }

    public function list()
    {
        $query = Order::with(['items.product', 'customer']);

        if (request()->has('search') && request()->get('search') !== '') {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('id', 'desc')->get();
        return view('admin.orders.list', compact('orders'));
    }

    public function invoice($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);
        $payments = Payment::where('order_id', $order->id)->orderBy('payment_date', 'asc')->get();
        return view('admin.orders.invoice', compact('order', 'payments'));
    }

    public function edit($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);
        
        $customers = Customer::orderBy('name')
            ->get(['id', 'name', 'phone', 'customer_type']);

        $products = Product::orderBy('name')
            ->get(['id', 'name', 'sku', 'sale_price', 'current_stock']);

        return view('admin.orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'outside_customer' => ['nullable', 'boolean'],
            'customer_id' => ['nullable', 'required_without:outside_customer', 'exists:customers,id'],
            'outside_customer_name' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:100'],
            'outside_customer_phone' => ['nullable', 'string', 'max:20'],
            'outside_customer_cnic' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:20'],
            'outside_customer_email' => ['nullable', 'required_if:outside_customer,1', 'email', 'max:20'],
            'outside_customer_from_add' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:255'],
            'outside_customer_to_add' => ['nullable', 'required_if:outside_customer,1', 'string', 'max:255'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($order, $data) {
            // 1. Restore old stock for all products in this order
            $oldItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($oldItems as $oldItem) {
                $prod = Product::find($oldItem->product_id);
                if ($prod) {
                    $prod->current_stock = (int) $prod->current_stock + (int) $oldItem->quantity;
                    $prod->save();
                    
                    // Adjust summary (subtract old sold/revenue/profit)
                    $summary = ProductSummary::where('product_id', $prod->id)->first();
                    if ($summary) {
                        $oldLineCost = ((float) ($prod->purchase_price ?? 0)) * $oldItem->quantity;
                        $summary->total_sold = max(0, ((int) $summary->total_sold) - $oldItem->quantity);
                        $summary->total_revenue = max(0, ((float) $summary->total_revenue) - $oldItem->final_price);
                        $summary->total_profit = ((float) $summary->total_profit) - ($oldItem->final_price - $oldLineCost);
                        $summary->current_stock = $prod->current_stock;
                        $summary->save();
                    }
                }
            }

            // Delete old items and stock movements
            OrderItem::where('order_id', $order->id)->delete();
            StockMovement::where('reference_id', $order->id)->where('type', 'out')->delete();

            // 2. Lock products for update and prepare new items
            $products = Product::lockForUpdate()
                ->whereIn('id', collect($data['items'])->pluck('product_id')->all())
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $preparedItems = [];
            $requestedQuantities = collect($data['items'])
                ->groupBy('product_id')
                ->map(fn ($items) => $items->sum(fn ($item) => (int) $item['quantity']));

            foreach ($requestedQuantities as $productId => $quantity) {
                $product = $products[(int) $productId];
                $stock = (int) ($product->current_stock ?? 0);

                if ($stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}.",
                    ]);
                }
            }

            foreach ($data['items'] as $item) {
                $product = $products[(int) $item['product_id']];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($product->sale_price ?? 0);
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];
            }

            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $orderDiscount = (float) ($data['discount'] ?? 0);
            $totalDiscount = min($subtotal, ($subtotal * ($discountPercent / 100)) + $orderDiscount);
            $finalAmount = max(0, $subtotal - $totalDiscount);
            $paidAmount = min((float) ($data['paid_amount'] ?? 0), $finalAmount);
            $remainingAmount = max(0, $finalAmount - $paidAmount);
            $userId = null;

            if (empty($data['outside_customer']) && ! empty($data['customer_id'])) {
                $userId = Customer::find($data['customer_id'])?->user_id;
            }

            if (! empty($data['outside_customer'])) {
                $customer = Customer::query()
                    ->where('cnic', $data['outside_customer_cnic'])
                    ->orWhere('email', $data['outside_customer_email'])
                    ->first();
                if ($customer) {
                    $userId = $customer->user_id;
                } else {
                    $user = new User();
                    $user->name = $data['outside_customer_name'];
                    $user->user_type = 'outside_customer';
                    $user->email = $data['outside_customer_email'];
                    $user->password = bcrypt('password');
                    $user->save();
                    $userId = $user->id;

                    $customerIns = DB::table('customers')->insertGetId([
                        'user_id' => $userId,
                        'name' => $data['outside_customer_name'],
                        'email' => $data['outside_customer_email'],
                        'phone' => $data['outside_customer_phone'] ?? null,
                        'cnic' => $data['outside_customer_cnic'],
                        'customer_type' => 'outside',
                        'from_add' => $data['outside_customer_from_add'],
                        'to_add' => $data['outside_customer_to_add'],
                        'created_at' => now(),
                    ]);
                }
            }

            $order->update([
                'user_id' => $userId,
                'total_amount' => $subtotal,
                'discount' => $totalDiscount,
                'discount_percent' => (int) round($discountPercent),
                'final_amount' => $finalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'remaining_amount_fixed' => $remainingAmount,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $lineDiscount = $subtotal > 0
                    ? ($preparedItem['total_price'] / $subtotal) * $totalDiscount
                    : 0;
                $lineFinalPrice = max(0, $preparedItem['total_price'] - $lineDiscount);
                $lineCost = ((float) ($preparedItem['product']->purchase_price ?? 0)) * $preparedItem['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $preparedItem['product']->id,
                    'quantity' => $preparedItem['quantity'],
                    'unit_price' => $preparedItem['unit_price'],
                    'total_price' => $preparedItem['total_price'],
                    'discount' => $lineDiscount,
                    'final_price' => $lineFinalPrice,
                    'cost_price' => $preparedItem['product']->purchase_price,
                ]);

                $preparedItem['product']->current_stock = (int) $preparedItem['product']->current_stock - $preparedItem['quantity'];
                $preparedItem['product']->save();

                StockMovement::create([
                    'product_id' => $preparedItem['product']->id,
                    'type' => 'out',
                    'quantity' => $preparedItem['quantity'],
                    'reference_id' => $order->id,
                ]);

                $summary = ProductSummary::firstOrNew(['product_id' => $preparedItem['product']->id]);
                $summary->total_sold = ((int) $summary->total_sold) + $preparedItem['quantity'];
                $summary->total_revenue = ((float) $summary->total_revenue) + $lineFinalPrice;
                $summary->total_profit = ((float) $summary->total_profit) + ($lineFinalPrice - $lineCost);
                $summary->current_stock = $preparedItem['product']->current_stock;
                $summary->last_sale_date = now()->toDateString();
                $summary->save();
            }
        });

        return redirect()
            ->route('order.invoice', $order->id)
            ->with('success', 'Order updated successfully.');
    }

    public function recordPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $order->remaining_amount],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $data) {
            // Create payment record
            Payment::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Update order paid and remaining amounts
            $newPaid = (float) $order->paid_amount + (float) $data['amount'];
            $newRemaining = max(0, (float) $order->remaining_amount - (float) $data['amount']);

            $order->update([
                'paid_amount' => $newPaid,
                'remaining_amount' => $newRemaining,
            ]);
        });

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }
}

