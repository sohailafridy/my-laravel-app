<x-app-layout>
    <x-slot name="title">Orders & Sales</x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-zinc-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Page Header Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-blue-600 tracking-tight">Orders & Sales</h1>
                </div>

                <!-- Controls Suite -->
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Integrated Search Bar -->
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center shadow-sm rounded-md overflow-hidden">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Order ID or Customer..."
                            class="h-10 w-64 border border-r-0 border-zinc-300 bg-white px-4 text-sm font-semibold text-zinc-950 focus:border-blue-600 focus:ring-blue-600 focus:outline-none rounded-l-md"
                        >
                        <button
                            type="submit"
                            class="inline-flex h-10 w-12 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white transition focus:outline-none rounded-r-md"
                            aria-label="Search"
                        >
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    <!-- Create New Order Button -->
                    <a
                        href="{{ route('admin.orders.create') }}"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-blue-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Create New Order
                    </a>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-0">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4 w-[12%]">Order ID</th>
                                <th class="px-6 py-4 w-[15%]">Date</th>
                                <th class="px-6 py-4 w-[25%]">Customer</th>
                                <th class="px-6 py-4 w-[13%]">Final Amount</th>
                                <th class="px-6 py-4 w-[12%]">Paid</th>
                                <th class="px-6 py-4 w-[12%]">Balance</th>
                                <th class="px-6 py-4 w-[11%] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50 transition">
                                    <!-- Order ID -->
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        #{{ $order->id }}
                                    </td>
                                    <!-- Date -->
                                    <td class="px-6 py-4 font-semibold text-slate-600">
                                        {{ $order->created_at ? $order->created_at->format('M d, Y') : '-' }}
                                    </td>
                                    <!-- Customer -->
                                    <td class="px-6 py-4 font-extrabold text-slate-900">
                                        @if($order->customer)
                                            <a href="{{ route('admin.customer.ledger', $order->customer->id) }}" class="hover:text-blue-600 transition">
                                                {{ $order->customer->name }}
                                            </a>
                                        @else
                                            <span class="text-slate-500">Walk-in Customer</span>
                                        @endif
                                    </td>
                                    <!-- Final Amount -->
                                    <td class="px-6 py-4 font-extrabold text-slate-900">
                                        PKR {{ number_format($order->final_amount, 2) }}
                                    </td>
                                    <!-- Paid -->
                                    <td class="px-6 py-4 font-semibold text-emerald-600">
                                        PKR {{ number_format($order->paid_amount, 2) }}
                                    </td>
                                    <!-- Balance -->
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        PKR {{ number_format($order->remaining_amount, 2) }}
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- View Button -->
                                            <a
                                                href="{{ route('order.invoice', $order->id) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 rounded-md text-xs font-bold text-slate-700 transition shadow-sm"
                                            >
                                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                View
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a
                                                href="{{ route('admin.orders.edit', $order->id) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-blue-200 bg-white hover:bg-blue-50 rounded-md text-xs font-bold text-blue-600 transition shadow-sm"
                                            >
                                                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Edit
                                            </a>

                                            <!-- Delete Button -->
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center p-1.5 border border-red-200 bg-white hover:bg-red-50 rounded-md text-red-600 transition shadow-sm"
                                                onclick="alert('Delete functionality is not configured yet.')"
                                            >
                                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                        No orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <!-- Totals Summary Row -->
                        @if ($orders->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50/50 border-t border-slate-200">
                                    <td colspan="2" class="px-6 py-4"></td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-900 uppercase tracking-wider">
                                        Total This View
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-900">
                                        PKR {{ number_format($orders->sum('final_amount'), 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-emerald-700">
                                        PKR {{ number_format($orders->sum('paid_amount'), 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-red-600">
                                        PKR {{ number_format($orders->sum('remaining_amount'), 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
