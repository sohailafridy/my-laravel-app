<x-app-layout>
    <x-slot name="title">Products</x-slot>
    @php
        // Example: Uncomment and use this logic if needed
        // if (request()->has('action')) {
        //     $action = request('action');
        //     if ($action == 'create') {
        //         echo  "Create Page";
        //     }
        // }

        $displayProducts = $products->isNotEmpty()
            ? $products
            : collect([
                (object) [
                    'image' => null,
                    'sku' => 'SK001',
                    'name' => 'P1',
                    'purchase_price' => 100,
                    'sale_price' => 150,
                    'current_stock' => 46,
                ],
            ]);
    @endphp

    <section class="min-h-[calc(100vh-4rem)] bg-zinc-50 px-5 py-6">
        <div class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <h1 class="text-3xl font-extrabold leading-tight text-blue-600">Products</h1>

            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center lg:w-auto">
                @if(!request()->has('action'))
                    <form action="#" method="GET" class="flex w-full sm:w-[315px]">
                        <input
                            type="search"
                            name="search"
                            placeholder="Search products..."
                            class="h-11 min-w-0 flex-1 rounded-l-md border border-zinc-300 bg-white px-4 text-sm font-medium text-zinc-700 shadow-sm transition placeholder:text-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                        >
                        <button
                            type="submit"
                            class="grid h-11 w-16 place-items-center rounded-r-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                            aria-label="Search products"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </button>
                    </form>
                    <a
                        href="{{ route('admin.products',['action' => 'create']) }}"
                        class="inline-flex h-11 items-center justify-center gap-3 rounded-md bg-blue-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Add Product
                    </a>
                @elseif(request()->has('action') && request('action') == 'create')
                    <a
                        href="{{ route('admin.products') }}"
                        class="inline-flex h-11 items-center justify-center gap-3 rounded-md bg-blue-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Cancel
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-md ring-1 ring-zinc-100">
            <div class="overflow-x-auto">
                @if(!request()->has('action'))    
                    <table class="min-w-full table-fixed text-left">
                        <thead class="border-b border-zinc-200 bg-zinc-50">
                            <tr>
                                <th class="w-20 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Image</th>
                                <th class="w-36 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">SKU</th>
                                <th class="w-44 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Name</th>
                                <th class="w-44 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Purchase Price</th>
                                <th class="w-44 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Sale Price</th>
                                <th class="w-36 px-4 py-5 text-xs font-extrabold uppercase text-slate-400">Stock</th>
                                <th class="w-32 px-4 py-5 text-right text-xs font-extrabold uppercase text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($displayProducts as $product)
                                <tr class="bg-white transition hover:bg-zinc-50">
                                    <td class="px-4 py-5">
                                        @if (! empty($product->image))
                                            <img
                                                src="{{ asset('storage/' .$product->image) }}"
                                                alt="{{ $product->name }}"
                                                class="h-12 w-12 rounded border border-zinc-300 object-cover p-0.5"
                                            >
                                        @else
                                            <div class="grid h-12 w-12 place-items-center rounded border border-zinc-300 bg-zinc-100 p-1 shadow-inner">
                                                <span class="text-sm font-black text-blue-600">{{ strtoupper(substr($product->sku ?? 'P', 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-sm font-extrabold text-slate-700">{{ $product->sku }}</td>
                                    <td class="px-4 py-5 text-sm font-extrabold text-black">{{ $product->name }}</td>
                                    <td class="px-4 py-5 text-sm font-extrabold text-slate-700">PKR {{ number_format($product->purchase_price, 2) }}</td>
                                    <td class="px-4 py-5 text-sm font-extrabold text-slate-700">PKR {{ number_format($product->sale_price, 2) }}</td>
                                    
                                    <td class="px-4 py-5">
                                        <span class="inline-flex min-w-[46px] justify-center rounded-md bg-emerald-700 px-3 py-1.5 text-xs font-extrabold text-white">
                                            {{ $product->current_stock }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a
                                                href="#"
                                                class="grid h-8 w-8 place-items-center rounded border border-cyan-400 bg-white text-cyan-500 transition hover:bg-cyan-50"
                                                aria-label="View product"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                                                </svg>
                                            </a>
                                            <a
                                                href="#"
                                                class="grid h-8 w-8 place-items-center rounded border border-blue-500 bg-white text-blue-600 transition hover:bg-blue-50"
                                                aria-label="Edit product"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                </svg>
                                            </a>
                                            <form action="#" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="button"
                                                    class="grid h-8 w-8 place-items-center rounded border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                    aria-label="Delete product"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                    <div class="mt-4 flex flex-col items-center justify-center">
                        <div class="mb-2 text-sm text-gray-500">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                        </div>
                        <div class="inline-flex rounded-md shadow-sm border border-gray-200 bg-white">
                            {{ $products->onEachSide(1)->links() }}
                        </div>
                    </div>
                @elseif(request()->has('action') && request('action') == 'create')
                    <div class="p-6">
                        <h2 class="mb-4 text-xl font-bold text-gray-800">Add New Product</h2>
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
                                <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Product
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
