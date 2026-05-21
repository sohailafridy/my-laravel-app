<x-app-layout>
    <x-slot name="title">Canceled Orders</x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-zinc-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">

            @if (session('success'))
                <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200" role="alert">
                    <div class="flex items-center font-bold">
                        <svg class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200" role="alert">
                    <div class="flex items-center font-bold">
                        <svg class="h-5 w-5 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            
            <!-- Page Header Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-zinc-700 tracking-tight flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-zinc-200 text-zinc-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        Canceled Orders
                    </h1>
                </div>

                <!-- Controls Suite -->
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Integrated Search Bar -->
                    <form method="GET" action="{{ route('admin.orders.cancelled') }}" class="flex items-center shadow-sm rounded-md overflow-hidden">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Order ID or Customer..."
                            class="h-10 w-64 border border-r-0 border-zinc-300 bg-white px-4 text-sm font-semibold text-zinc-950 focus:border-zinc-500 focus:ring-zinc-500 focus:outline-none rounded-l-md"
                        >
                        <button
                            type="submit"
                            class="inline-flex h-10 w-12 items-center justify-center bg-zinc-600 hover:bg-zinc-700 text-white transition focus:outline-none rounded-r-md"
                            aria-label="Search"
                        >
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
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
                                <th class="px-6 py-4 w-[35%]">Customer</th>
                                <th class="px-6 py-4 w-[15%]">Original Amount</th>
                                <th class="px-6 py-4 w-[13%] text-center">Status</th>
                                <th class="px-6 py-4 w-[10%] text-center">Actions</th>
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
                                    <!-- Original Amount -->
                                    <td class="px-6 py-4 font-extrabold text-slate-400 line-through">
                                        PKR {{ number_format($order->final_amount, 2) }}
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 border border-zinc-200 uppercase tracking-wide">
                                            Canceled
                                        </span>
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center">
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
                                                View Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                        No canceled orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <!-- Totals Summary Row -->
                        @if ($orders->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50/50 border-t border-slate-200">
                                    <td colspan="2" class="px-6 py-4"></td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-500 uppercase tracking-wider">
                                        Total Canceled Value
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-slate-400 line-through">
                                        PKR {{ number_format($orders->sum('final_amount'), 2) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
