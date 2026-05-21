<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\ProductSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authorized user can cancel and delete an order and revert all data', function () {
    $user = User::factory()->create(['user_type' => 'admin']);
    $customer = Customer::create([
        'user_id' => $user->id,
        'name' => 'John Doe',
        'customer_type' => 'customer',
        'cnic' => '12345-6789012-3',
        'email' => 'john@example.com',
    ]);

    $product = Product::create([
        'name' => 'Test Product',
        'sku' => 'TESTSKU',
        'sale_price' => 100,
        'purchase_price' => 60,
        'current_stock' => 10,
    ]);

    // Create an order
    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 200,
        'discount' => 10,
        'discount_percent' => 5,
        'final_amount' => 190,
        'paid_amount' => 100,
        'remaining_amount' => 90,
        'remaining_amount_fixed' => 90,
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 100,
        'total_price' => 200,
        'discount' => 10,
        'final_price' => 190,
        'cost_price' => 60,
    ]);

    // Simulate stock deduction and summary increase upon order creation
    $product->current_stock = 8;
    $product->save();

    StockMovement::create([
        'product_id' => $product->id,
        'type' => 'out',
        'quantity' => 2,
        'reference_id' => $order->id,
    ]);

    ProductSummary::create([
        'product_id' => $product->id,
        'total_sold' => 2,
        'total_revenue' => 190,
        'total_profit' => 70, // 190 revenue - 120 cost
        'current_stock' => 8,
        'last_sale_date' => now()->toDateString(),
    ]);

    Payment::create([
        'user_id' => $user->id,
        'order_id' => $order->id,
        'amount' => 100,
        'payment_date' => now()->toDateString(),
        'payment_method' => 'Cash',
    ]);

    // Make the DELETE request
    $response = $this
        ->actingAs($user)
        ->delete("/admin/order/delete/{$order->id}");

    $response->assertRedirect('/admin/order/list');
    $response->assertSessionHas('success');

    // Assert order and related items are deleted from database
    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    $this->assertDatabaseMissing('order_items', ['id' => $orderItem->id]);
    $this->assertDatabaseMissing('payments', ['order_id' => $order->id]);
    $this->assertDatabaseMissing('stock_movements', ['reference_id' => $order->id]);

    // Assert product stock is reverted
    $product->refresh();
    $this->assertEquals(10, $product->current_stock);

    // Assert product summary is adjusted
    $summary = ProductSummary::where('product_id', $product->id)->first();
    $this->assertEquals(0, $summary->total_sold);
    $this->assertEquals(0, $summary->total_revenue);
    $this->assertEquals(0, $summary->total_profit);
    $this->assertEquals(10, $summary->current_stock);
});
