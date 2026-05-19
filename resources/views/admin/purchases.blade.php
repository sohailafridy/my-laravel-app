<x-app-layout>
    <x-slot name="title">Purchases</x-slot>

    <section class="min-h-[calc(100vh-4rem)] bg-zinc-50 px-5 py-7 sm:px-6 lg:px-9">
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800 shadow-sm">
                <svg class="h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-800 shadow-sm">
                <svg class="h-5 w-5 shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="mb-8 flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-4xl font-extrabold leading-tight text-blue-600">
                Inventory Purchase History
            </h1>

            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center xl:w-auto">
                <form
                    method="GET"
                    action="{{ route('admin.purchases') }}"
                    class="flex h-16 w-full overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-200 transition focus-within:ring-2 focus-within:ring-blue-500 sm:w-[474px]"
                >
                    <input
                        type="search"
                        name="sku"
                        value="{{ $sku ?? '' }}"
                        placeholder="Search by Product/SKU..."
                        class="min-w-0 flex-1 border-0 bg-white px-6 text-xl font-medium text-zinc-700 placeholder:text-zinc-500 focus:ring-0"
                    >
                    <button
                        type="submit"
                        class="grid h-16 w-24 place-items-center bg-blue-600 text-white transition hover:bg-blue-700"
                        aria-label="Search purchases"
                    >
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                        </svg>
                    </button>
                </form>

                <a
                    href="{{ route('admin.restock') }}"
                    class="inline-flex h-16 items-center justify-center gap-4 rounded-lg bg-blue-600 px-8 text-xl font-extrabold text-white shadow-sm transition hover:bg-blue-700 sm:min-w-[294px]"
                >
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l1.6 10.4a2 2 0 0 0 2 1.7h7.8a2 2 0 0 0 1.95-1.56L20 6H6" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20.5h.01M17 20.5h.01M14 9h4m-2-2v4" />
                    </svg>
                    <span>Record Purchase</span>
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-zinc-100">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1040px] table-fixed text-left">
                    <thead class="border-b-2 border-zinc-100 bg-zinc-50/80">
                        <tr>
                            <th class="w-24 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">ID</th>
                            <th class="w-52 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Date</th>
                            <th class="w-72 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Product</th>
                            <th class="w-28 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Qty</th>
                            <th class="w-56 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Unit Cost</th>
                            <th class="w-60 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Total Cost</th>
                            <th class="w-48 px-6 py-8 text-sm font-extrabold uppercase text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($purchases as $purchase)
                            <tr class="bg-white transition hover:bg-zinc-50">
                                <td class="px-6 py-8 text-2xl font-medium text-zinc-950">
                                    #{{ $purchase->id }}
                                </td>
                                <td class="px-6 py-8 text-2xl font-medium text-zinc-950">
                                    {{ \Carbon\Carbon::parse($purchase->purchase_date ?? $purchase->created_at)->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-8">
                                    <div class="text-2xl font-extrabold leading-7 text-zinc-950">{{ $purchase->product_name ?? '-' }}</div>
                                    <div class="mt-2 text-xl font-medium leading-6 text-zinc-600">{{ $purchase->product_sku ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-8 text-2xl font-medium text-zinc-950">
                                    {{ number_format($purchase->quantity) }}
                                </td>
                                <td class="px-6 py-8 text-2xl font-medium text-zinc-950">
                                    PKR {{ number_format($purchase->unit_cost, 2) }}
                                </td>
                                <td class="px-6 py-8 text-2xl font-extrabold text-zinc-950">
                                    PKR {{ number_format($purchase->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-8">
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            class="grid h-11 w-12 place-items-center rounded-md border border-blue-500 bg-white text-blue-600 transition hover:bg-blue-50"
                                            aria-label="Edit purchase"
                                        >
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                            </svg>
                                        </button>
                                        <a
                                            href="{{ route('admin.purchases.reverse', $purchase->id) }}"
                                            class="grid h-11 w-12 place-items-center rounded-md border border-red-500 bg-white text-red-600 transition hover:bg-red-50"
                                            aria-label="Reverse purchase"
                                            onclick="return confirm('Are you sure you want to reverse this purchase? This will decrease the product stock.');"
                                        >
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14 4 9m0 0 5-5M4 9h11a5 5 0 0 1 0 10h-3" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-lg font-bold text-slate-400">
                                    No purchase records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($purchases->hasPages())
            <div class="mt-5 flex justify-center">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
