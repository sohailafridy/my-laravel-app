<x-app-layout>
    <x-slot name="title">Create Order</x-slot>

    @php
        $productOptions = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sale_price' => (float) ($product->sale_price ?? 0),
                'current_stock' => (int) ($product->current_stock ?? 0),
            ];
        })->values();
    @endphp

    <section class="min-h-[calc(100vh-4rem)] bg-zinc-50 px-5 py-7 sm:px-6 lg:px-9">
        <div class="mb-6 flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-3xl font-extrabold leading-tight text-blue-600">
                Orders &amp; Sales
            </h1>

            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center xl:w-auto">
                <form action="#" method="GET" class="flex h-11 w-full overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-zinc-200 sm:w-[315px]">
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
            x-data="orderCreate({{ Illuminate\Support\Js::from($productOptions) }})"
            class="mx-auto max-w-5xl rounded-xl bg-white px-6 py-6 shadow-sm ring-1 ring-zinc-200 sm:px-8"
        >
            <h2 class="mb-6 text-xl font-extrabold text-zinc-950">Add New Order</h2>

            <form action="#" method="POST" class="grid gap-6 lg:grid-cols-[304px_minmax(0,1fr)]" @submit.prevent>
                @csrf

                <aside class="rounded-xl bg-zinc-50 p-4 shadow-md ring-1 ring-zinc-100">
                    <label class="mb-6 flex items-center gap-2 text-sm font-extrabold text-zinc-950">
                        <input
                            type="checkbox"
                            x-model="outsideCustomer"
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
                                    <option value="{{ $customer->id }}">
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
                    <div class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-zinc-100">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[608px] table-fixed text-left">
                                <thead class="border-b border-zinc-200 bg-zinc-50">
                                    <tr>
                                        <th class="w-[52%] px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Product</th>
                                        <th class="w-[15%] px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Price</th>
                                        <th class="w-[13%] px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Qty</th>
                                        <th class="w-[20%] px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="row.key">
                                        <tr class="bg-white">
                                            <td class="px-4 py-4">
                                                <select
                                                    x-model="row.product_id"
                                                    @change="setProduct(row)"
                                                    class="h-10 w-full rounded-md border-zinc-300 bg-white text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                                                >
                                                    <option value="">Choose...</option>
                                                    <template x-for="product in products" :key="product.id">
                                                        <option :value="product.id" x-text="productLabel(product)"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="px-4 py-4 text-base font-extrabold text-zinc-950">
                                                <span x-text="money(row.price)"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    x-model.number="row.qty"
                                                    class="h-10 w-12 rounded-md border-zinc-300 bg-white text-center text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500"
                                                >
                                            </td>
                                            <td class="px-4 py-4 text-base font-extrabold text-zinc-950">
                                                <span x-text="money(rowTotal(row))"></span>
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
                            class="inline-flex h-10 items-center justify-center rounded-md bg-blue-600 px-12 text-base font-extrabold text-white shadow-sm transition hover:bg-blue-700 sm:min-w-[216px]"
                        >
                            Complete Order
                        </button>
                        <a
                            href="{{ route('dashboard') }}"
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
        function orderCreate(products) {
            return {
                products: products,
                rows: [{ key: Date.now(), product_id: '', price: 0, qty: 1 }],
                outsideCustomer: false,
                discountPercent: 0,
                orderDiscount: 0,
                paidAmount: 0,
                addRow() {
                    this.rows.push({ key: Date.now() + Math.random(), product_id: '', price: 0, qty: 1 });
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
