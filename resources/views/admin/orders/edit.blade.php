<x-app-layout>
    <x-slot name="title">Edit Order #{{ $order->id }}</x-slot>

    @php
        // Calculate effective stock by adding back the quantity currently in this order
        $productOptions = $products->map(function ($product) use ($order) {
            $existingItem = $order->items->firstWhere('product_id', $product->id);
            $existingQty = $existingItem ? $existingItem->quantity : 0;
            
            return [
                'id' => (string) $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sale_price' => (float) ($product->sale_price ?? 0),
                'current_stock' => (int) ($product->current_stock ?? 0) + (int) $existingQty,
            ];
        })->values();

        // Prepare existing order items for Alpine.js initialization
        $orderItemsData = $order->items->map(function ($item) {
            return [
                'key' => $item->id,
                'product_id' => (string) $item->product_id,
                'price' => (float) $item->unit_price,
                'qty' => (int) $item->quantity,
            ];
        })->values();
        
        // Calculate the stored flat discount by subtracting the percentage portion
        $totalAmountVal = (float) ($order->total_amount ?? 0);
        $discountPercentVal = (float) ($order->discount_percent ?? 0);
        $totalDiscountVal = (float) ($order->discount ?? 0);
        $flatDiscount = max(0, $totalDiscountVal - ($totalAmountVal * ($discountPercentVal / 100)));
    @endphp

    <section class="min-h-[calc(100vh-4rem)] bg-zinc-50 px-5 py-7 sm:px-6 lg:px-9">
        <div class="mb-6 flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-3xl font-extrabold leading-tight text-blue-600">
                Orders &amp; Sales
            </h1>

            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center xl:w-auto">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="flex h-11 w-full overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-zinc-200 sm:w-[315px]">
                    <input
                        type="search"
                        name="search"
                        placeholder="Order ID or Customer..."
                        class="min-w-0 flex-1 border-0 bg-white px-4 text-sm font-medium text-zinc-700 placeholder:text-zinc-500 focus:ring-0"
                    >
                    <button
                        type="submit"
                        class="grid h-11 w-16 place-items-center bg-blue-600 text-white transition hover:bg-blue-700"
                        aria-label="Search orders"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                    </button>
                </form>

                <a
                    href="{{ route('admin.orders.create') }}"
                    class="inline-flex h-11 items-center justify-center gap-3 rounded-md bg-blue-600 px-6 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700 sm:min-w-[207px]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12h8" />
                        <circle cx="12" cy="12" r="8" />
                    </svg>
                    Create New Order
                </a>
            </div>
        </div>

        <div
            x-data="orderEdit({{ Illuminate\Support\Js::from($productOptions) }}, {{ Illuminate\Support\Js::from($orderItemsData) }})"
            class="mx-auto max-w-5xl rounded-xl bg-white px-6 py-6 shadow-sm ring-1 ring-zinc-200 sm:px-8"
        >
            <h2 class="mb-6 text-xl font-extrabold text-zinc-950">Edit Order #{{ $order->id }}</h2>

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200" role="alert">
                    <div class="flex items-center font-bold mb-2">
                        <svg class="h-5 w-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Please correct the following errors:
                    </div>
                    <ul class="list-disc pl-5 space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200" role="alert">
                    <div class="flex items-center font-bold">
                        <svg class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="grid gap-6 lg:grid-cols-[304px_minmax(0,1fr)]">
                @csrf

                <aside class="rounded-xl bg-zinc-50 p-4 shadow-md ring-1 ring-zinc-100">
                    <label class="mb-6 flex items-center gap-2 text-sm font-extrabold text-zinc-950">
                        <input
                            type="checkbox"
                            x-model="outsideCustomer"
                            name="outside_customer"
                            value="1"
                            class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                        >
                        Outside Customer
                    </label>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-extrabold uppercase text-zinc-950">Customer</label>
                            <select
                                name="customer_id"
                                class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                                :disabled="outsideCustomer"
                            >
                                <option value="">Select Customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ ($order->customer && $order->customer->id === $customer->id) ? 'selected' : '' }}>
                                        {{ $customer->name }}{{ $customer->phone ? ' - '.$customer->phone : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold uppercase text-zinc-950">Discount in %</label>
                            <input
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                x-model.number="discountPercent"
                                name="discount_percent"
                                class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold uppercase text-zinc-950">Order Discount (PKR)</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                x-model.number="orderDiscount"
                                name="discount"
                                class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>

                        <div class="border-t border-zinc-300 pt-5">
                            <label class="mb-2 block text-sm font-extrabold uppercase text-zinc-950">Paid Amount (PKR)</label>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                x-model.number="paidAmount"
                                name="paid_amount"
                                class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold uppercase text-zinc-950">Remaining Balance (PKR)</label>
                            <input
                                type="text"
                                readonly
                                :value="money(remainingBalance)"
                                class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div class="mt-6 border-t border-zinc-300 pt-4">
                        <h3 class="mb-3 text-sm font-extrabold uppercase text-slate-600">Order Totals</h3>
                        <div class="space-y-3 text-sm font-medium text-zinc-950">
                            <div class="flex items-center justify-between gap-4">
                                <span>Subtotal:</span>
                                <span>PKR <span x-text="money(subtotal)"></span></span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Discount:</span>
                                <span class="text-red-600">-PKR <span x-text="money(totalDiscount)"></span></span>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-4 border-t border-zinc-300 pt-3 text-base font-extrabold">
                            <span>Final Total:</span>
                            <span class="text-xl text-blue-600">PKR <span x-text="money(finalTotal)"></span></span>
                        </div>
                    </div>
                </aside>

                <div class="min-w-0">
                    <!-- Outside Customer Information Form -->
                    <div x-show="outsideCustomer" class="mb-6 rounded-xl bg-zinc-50 p-5 shadow ring-1 ring-zinc-200">
                        <div class="mb-3 text-base font-extrabold uppercase text-zinc-700">Outside Customer Information</div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-bold mb-1">Name</label>
                                <input type="text" x-model="outsideCustomerName" name="outside_customer_name" placeholder="Full Name" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1">Phone</label>
                                <input type="text" x-model="outsideCustomerPhone" name="outside_customer_phone" placeholder="Phone" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1">CNIC</label>
                                <input type="text" x-model="outsideCustomerCnic" name="outside_customer_cnic" placeholder="CNIC" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1">Email</label>
                                <input type="email" x-model="outsideCustomerEmail" name="outside_customer_email" placeholder="Email Address" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1">From Address</label>
                                <input type="text" x-model="outsideCustomerFromAddress" name="outside_customer_from_add" placeholder="From Address" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1">To Address</label>
                                <input type="text" x-model="outsideCustomerToAddress" name="outside_customer_to_add" placeholder="To Address" class="w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-zinc-100">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-0 table-fixed text-left">
                                <thead class="border-b border-zinc-200 bg-zinc-50">
                                    <tr>
                                        <th class="w-[45%] px-2 py-3 text-xs font-extrabold uppercase text-slate-400">Product</th>
                                        <th class="w-[15%] px-2 py-3 text-xs font-extrabold uppercase text-slate-400">Price</th>
                                        <th class="w-[12%] px-2 py-3 text-xs font-extrabold uppercase text-slate-400">Qty</th>
                                        <th class="w-[16%] px-2 py-3 text-xs font-extrabold uppercase text-slate-400">Total</th>
                                        <th class="w-[4%] px-1 py-3 text-xs font-extrabold uppercase text-slate-400">
                                            <span class="sr-only">Remove</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="row.key">
                                        <tr class="bg-white">
                                            <td class="px-2 py-3">
                                                <div class="relative w-full" x-data="{
                                                    get selectedProduct() {
                                                        return products.find(p => String(p.id) === String(row.product_id));
                                                    },
                                                    get filteredProducts() {
                                                        const query = (row.searchQuery || '').toLowerCase();
                                                        return products.filter(p => {
                                                            const isSelectedElsewhere = rows.some((r, i) => i !== index && String(r.product_id) === String(p.id));
                                                            if (isSelectedElsewhere) return false;
                                                            return p.name.toLowerCase().includes(query) || (p.sku && p.sku.toLowerCase().includes(query));
                                                        });
                                                    }
                                                }" @click.away="row.isOpen = false">
                                                    <!-- Trigger Button -->
                                                    <button
                                                        type="button"
                                                        @click="row.isOpen = !row.isOpen; if(row.isOpen) { $nextTick(() => $refs.searchInput.focus()) }"
                                                        class="h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-left text-sm font-medium text-zinc-950 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none flex items-center justify-between transition-all"
                                                    >
                                                        <span x-show="selectedProduct" class="truncate">
                                                            <span class="font-extrabold text-zinc-900" x-text="selectedProduct ? selectedProduct.name : ''"></span>
                                                            <span class="text-xs text-zinc-400 font-semibold" x-show="selectedProduct && selectedProduct.sku" x-text="selectedProduct ? ' (' + selectedProduct.sku + ')' : ''"></span>
                                                        </span>
                                                        <span x-show="!selectedProduct" class="text-zinc-500">Choose...</span>
                                                        <svg class="h-4 w-4 text-zinc-500 transition-transform" :class="row.isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <!-- Hidden input for standard form submission -->
                                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="row.product_id">

                                                    <!-- Dropdown Menu -->
                                                    <div
                                                        x-show="row.isOpen"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="opacity-0 scale-95"
                                                        x-transition:enter-end="opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="opacity-100 scale-100"
                                                        x-transition:leave-end="opacity-0 scale-95"
                                                        class="absolute z-50 mt-1 w-full rounded-md bg-white border border-zinc-200 shadow-lg max-h-60 overflow-y-auto focus:outline-none"
                                                        x-cloak
                                                    >
                                                        <!-- Sticky Search Input -->
                                                        <div class="p-2 bg-white sticky top-0 border-b border-zinc-100">
                                                            <input
                                                                type="text"
                                                                x-ref="searchInput"
                                                                x-model="row.searchQuery"
                                                                @keydown.escape.stop="row.isOpen = false"
                                                                @click.stop
                                                                placeholder="Search product..."
                                                                class="h-8 w-full text-xs rounded border-zinc-300 focus:border-blue-500 focus:ring-blue-500 bg-zinc-50 font-medium px-2"
                                                            >
                                                        </div>

                                                        <!-- Scrollable List -->
                                                        <div class="py-1">
                                                            <template x-for="product in filteredProducts" :key="product.id">
                                                                <button
                                                                    type="button"
                                                                    @click="row.product_id = product.id; setProduct(row); row.isOpen = false; row.searchQuery = ''"
                                                                    class="w-full text-left px-3 py-2 text-xs hover:bg-zinc-100 transition-colors font-medium text-zinc-950 flex flex-col justify-start border-b border-zinc-50 last:border-0"
                                                                >
                                                                    <span class="font-extrabold text-zinc-900" x-text="product.name"></span>
                                                                    <span class="text-[10px] text-zinc-400 font-semibold mt-0.5" x-show="product.sku" x-text="'SKU: ' + product.sku"></span>
                                                                </button>
                                                            </template>
                                                            
                                                            <!-- No results state -->
                                                            <div x-show="filteredProducts.length === 0" class="p-3 text-xs text-zinc-400 italic text-center font-medium">
                                                                No products found
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-2 py-3 text-base font-extrabold text-zinc-950">
                                                <span x-text="money(row.price)"></span>
                                            </td>
                                            <td class="px-2 py-3">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    x-model.number="row.qty"
                                                    :name="`items[${index}][quantity]`"
                                                    :class="isOutOfStock(row) ? 'border-red-500 text-red-900 focus:border-red-500 focus:ring-red-500 bg-red-50' : 'border-zinc-300 text-zinc-950 focus:border-blue-500 focus:ring-blue-500 bg-white'"
                                                    class="h-10 w-16 rounded-md text-center text-sm font-medium transition"
                                                >
                                                <div x-show="isOutOfStock(row)" class="text-[10px] font-extrabold text-red-600 mt-1" x-text="`Max: ${getStock(row)}`"></div>
                                            </td>
                                            <td class="px-2 py-3 text-base font-extrabold text-zinc-950">
                                                <span x-text="money(rowTotal(row))"></span>
                                            </td>
                                            <td class="px-1 py-3 text-right align-middle">
                                                <button
                                                    type="button"
                                                    x-show="index > 0"
                                                    @click="removeRow(index)"
                                                    class="inline-grid h-6 w-6 place-items-center rounded-md border border-red-500 text-red-600 transition hover:bg-red-50 p-0"
                                                    aria-label="Remove item row"
                                                    style="vertical-align:middle;"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="addRow"
                        x-show="rows.length < products.length"
                        class="mt-0 inline-flex h-8 items-center justify-center gap-2 rounded border border-blue-600 bg-white px-3 text-sm font-medium text-blue-600 transition hover:bg-blue-50"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Add Item Row
                    </button>

                    <div class="mt-12 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button
                            type="submit"
                            :disabled="hasStockError"
                            :class="hasStockError ? 'opacity-50 cursor-not-allowed bg-zinc-400 hover:bg-zinc-400' : 'bg-blue-600 hover:bg-blue-700'"
                            class="inline-flex h-10 items-center justify-center rounded-md px-12 text-base font-extrabold text-white shadow-sm transition sm:min-w-[216px]"
                        >
                            Update Order
                        </button>
                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="inline-flex h-10 items-center justify-center rounded-md bg-zinc-50 px-7 text-base font-medium text-zinc-950 transition hover:bg-zinc-100"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        function orderEdit(products, initialRows) {
            return {
                products: products,
                rows: initialRows.map(r => ({ ...r, searchQuery: '', isOpen: false })),
                outsideCustomer: {{ ($order->customer && $order->customer->customer_type === 'outside') ? 'true' : 'false' }},
                // Outside customer fields
                outsideCustomerName: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->name) : "" }}',
                outsideCustomerPhone: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->phone) : "" }}',
                outsideCustomerCnic: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->cnic) : "" }}',
                outsideCustomerEmail: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->email) : "" }}',
                outsideCustomerFromAddress: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->from_add) : "" }}',
                outsideCustomerToAddress: '{{ ($order->customer && $order->customer->customer_type === "outside") ? e($order->customer->to_add) : "" }}',
                discountPercent: {{ (float) ($order->discount_percent ?? 0) }},
                orderDiscount: {{ (float) $flatDiscount }},
                paidAmount: {{ (float) ($order->paid_amount ?? 0) }},
                addRow() {
                    if (this.rows.length < this.products.length) {
                        this.rows.push({ key: Date.now() + Math.random(), product_id: '', price: 0, qty: 1, searchQuery: '', isOpen: false });
                    }
                },
                removeRow(index) {
                    if (index > 0) {
                        this.rows.splice(index, 1);
                    }
                },
                setProduct(row) {
                    const product = this.products.find((item) => String(item.id) === String(row.product_id));
                    row.price = product ? Number(product.sale_price || 0) : 0;
                },
                productLabel(product) {
                    const sku = product.sku ? ` (${product.sku})` : '';
                    return `${product.name}${sku}`;
                },
                rowTotal(row) {
                    return Number(row.price || 0) * Number(row.qty || 0);
                },
                money(value) {
                    return Number(value || 0).toFixed(2);
                },
                isOutOfStock(row) {
                    if (!row.product_id) return false;
                    const product = this.products.find((p) => String(p.id) === String(row.product_id));
                    if (!product) return false;
                    return Number(row.qty || 0) > Number(product.current_stock || 0);
                },
                getStock(row) {
                    if (!row.product_id) return 0;
                    const product = this.products.find((p) => String(p.id) === String(row.product_id));
                    return product ? Number(product.current_stock || 0) : 0;
                },
                get hasStockError() {
                    return this.rows.some(row => this.isOutOfStock(row));
                },
                get subtotal() {
                    return this.rows.reduce((total, row) => total + this.rowTotal(row), 0);
                },
                get percentDiscount() {
                    return this.subtotal * (Number(this.discountPercent || 0) / 100);
                },
                get totalDiscount() {
                    return Math.min(this.subtotal, this.percentDiscount + Number(this.orderDiscount || 0));
                },
                get finalTotal() {
                    return Math.max(0, this.subtotal - this.totalDiscount);
                },
                get remainingBalance() {
                    return Math.max(0, this.finalTotal - Number(this.paidAmount || 0));
                },
            };
        }
    </script>
</x-app-layout>
