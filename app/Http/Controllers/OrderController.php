<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;

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
}
