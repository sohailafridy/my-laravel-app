<x-app-layout>
    <x-slot name="title">Customer Ledger - {{ $customer->name }}</x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-zinc-50 py-8 px-4 sm:px-6 lg:px-8 print:bg-white print:py-0 print:px-0">
        <div x-data="paymentModal()" class="max-w-7xl mx-auto space-y-8 relative">
            
            <!-- Customer Info Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 print:hidden">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $customer->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wide border border-amber-200">
                            {{ $customer->customer_type ?? 'OUTSIDE' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-slate-500 font-medium flex-wrap">
                        @if($customer->phone)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $customer->phone }}
                            </span>
                        @endif
                        @if($customer->phone && $customer->email)
                            <span class="text-slate-300">|</span>
                        @endif
                        @if($customer->email)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $customer->email }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-blue-500 rounded-md bg-white text-sm font-bold text-blue-600 transition hover:bg-blue-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Edit Profile
                    </a>
                    
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-md bg-white text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Ledger
                    </button>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 rounded-md text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Print Customer Info Title Banner -->
            <div class="hidden print:block pb-6 border-b border-slate-200 mb-8">
                <h1 class="text-3xl font-bold text-slate-900">{{ $customer->name }} - Customer Ledger</h1>
                <p class="text-sm text-slate-500 mt-1">Printed on: {{ now()->format('F d, Y h:i A') }}</p>
                <div class="mt-4 grid grid-cols-2 gap-4 text-xs font-semibold text-slate-700">
                    <div><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</div>
                    <div><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</div>
                </div>
            </div>

            @if (session('success'))
                <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200 shadow-sm print:hidden" role="alert">
                    <div class="flex items-center font-bold">
                        <svg class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- 4 KPI Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Volume -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 text-center flex flex-col justify-between">
                    <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Volume</span>
                    <span class="text-2xl font-black text-slate-900 my-2">PKR {{ number_format($totalVolume, 2) }}</span>
                    <span class="text-xs font-semibold text-slate-400">From {{ $orders->count() }} Orders</span>
                </div>

                <!-- Total Paid -->
                <div class="bg-emerald-50 rounded-xl p-6 shadow-sm border border-emerald-100 text-center flex flex-col justify-between">
                    <span class="text-xs font-extrabold text-emerald-600 uppercase tracking-wider">Total Paid</span>
                    <span class="text-2xl font-black text-emerald-700 my-2">PKR {{ number_format($totalPaid, 2) }}</span>
                    <span class="text-xs font-semibold text-emerald-500">Recorded Payments</span>
                </div>

                <!-- Outstanding Balance -->
                <div class="bg-rose-50 rounded-xl p-6 shadow-sm border border-rose-100 text-center flex flex-col justify-between">
                    <span class="text-xs font-extrabold text-rose-600 uppercase tracking-wider">Outstanding Balance</span>
                    <span class="text-2xl font-black text-rose-700 my-2">PKR {{ number_format($outstandingBalance, 2) }}</span>
                    <span class="text-xs font-semibold text-rose-500">Unpaid Amount</span>
                </div>

                <!-- Total Savings -->
                <div class="bg-cyan-50 rounded-xl p-6 shadow-sm border border-cyan-100 text-center flex flex-col justify-between">
                    <span class="text-xs font-extrabold text-cyan-600 uppercase tracking-wider">Total Savings</span>
                    <span class="text-2xl font-black text-cyan-700 my-2">PKR {{ number_format($totalSavings, 2) }}</span>
                    <span class="text-xs font-semibold text-cyan-500">Loyalty Discounts</span>
                </div>
            </div>

            <!-- Detailed Transaction History Section -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-base font-extrabold text-slate-800 uppercase tracking-wide">Detailed Transaction History</h2>
                </div>

                <!-- Card Body (Table) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-0">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-3 w-[15%]">Date</th>
                                <th class="px-6 py-3 w-[30%]">Reference</th>
                                <th class="px-6 py-3 w-[15%]">Final Amount</th>
                                <th class="px-6 py-3 w-[15%]">Paid</th>
                                <th class="px-6 py-3 w-[15%]">Balance</th>
                                <th class="px-6 py-3 w-[10%] text-center">Status</th>
                                <th class="px-6 py-3 w-[10%] text-center print:hidden">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-slate-50 transition">
                                    <!-- Date -->
                                    <td class="px-6 py-4 font-semibold text-slate-600">
                                        {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                                    </td>
                                    <!-- Reference -->
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">Order #{{ $order->id }}</div>
                                        <div class="text-xs text-slate-400 font-semibold mt-0.5">Total: PKR {{ number_format($order->total_amount, 2) }}</div>
                                    </td>
                                    <!-- Final Amount -->
                                    <td class="px-6 py-4 font-extrabold text-slate-900">
                                        PKR {{ number_format($order->final_amount, 2) }}
                                    </td>
                                    <!-- Paid Amount -->
                                    <td class="px-6 py-4 font-extrabold text-emerald-600">
                                        PKR {{ number_format($order->paid_amount, 2) }}
                                    </td>
                                    <!-- Balance (Remaining) -->
                                    <td class="px-6 py-4 font-extrabold text-rose-600">
                                        PKR {{ number_format($order->remaining_amount, 2) }}
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @if($order->remaining_amount <= 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wide">
                                                Cleared
                                            </span>
                                        @elseif($order->paid_amount > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide">
                                                Partial
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200 uppercase tracking-wide">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Action Button -->
                                    <td class="px-6 py-4 text-center print:hidden">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($order->remaining_amount > 0)
                                                <button 
                                                    type="button" 
                                                    @click="openPayModal({{ $order->id }}, {{ $order->remaining_amount }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-extrabold transition shadow-sm whitespace-nowrap"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    Pay
                                                </button>
                                            @endif
                                            <a href="{{ route('order.invoice', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1 border border-slate-200 rounded text-xs font-bold text-slate-700 transition hover:bg-slate-50 whitespace-nowrap">
                                                Invoice
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                        No order transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <!-- Totals Row at the bottom -->
                        @if($orders->count() > 0)
                            <tfoot class="border-t-2 border-slate-200 bg-slate-50 font-black text-slate-800">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-right text-xs font-extrabold uppercase tracking-wider text-slate-400">
                                        Total This View
                                    </td>
                                    <!-- Sum of Final Amount -->
                                    <td class="px-6 py-4 text-slate-900 font-extrabold">
                                        PKR {{ number_format($totalVolume, 2) }}
                                    </td>
                                    <!-- Sum of Paid Amount -->
                                    <td class="px-6 py-4 text-emerald-600 font-extrabold">
                                        PKR {{ number_format($orders->sum('paid_amount'), 2) }}
                                    </td>
                                    <!-- Sum of Remaining Amount -->
                                    <td class="px-6 py-4 text-rose-600 font-extrabold" colspan="3">
                                        PKR {{ number_format($outstandingBalance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Payment History Section -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-base font-extrabold text-slate-800 uppercase tracking-wide">Payment History</h2>
                </div>

                <!-- Card Body (Table) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-0">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-3 w-[20%]">Date</th>
                                <th class="px-6 py-3 w-[20%]">Payment Method</th>
                                <th class="px-6 py-3 w-[20%]">Reference</th>
                                <th class="px-6 py-3 w-[25%]">Notes</th>
                                <th class="px-6 py-3 w-[15%] text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-600">
                                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 capitalize">
                                        {{ $payment->payment_method ?? 'Cash' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ $payment->order_id ? 'Order #' . $payment->order_id : 'Manual Payment' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-500">
                                        {{ $payment->notes ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-emerald-600">
                                        PKR {{ number_format($payment->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                        No payments recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Modal Overlay -->
            <div 
                x-show="isOpen" 
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
                aria-labelledby="modal-title" 
                role="dialog" 
                aria-modal="true"
            >
                <!-- Backdrop overlay -->
                <div 
                    x-show="isOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                    @click="closePayModal"
                ></div>

                <!-- Modal Content Panel -->
                <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                    <div 
                        x-show="isOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    >
                        <!-- Modal Header -->
                        <div class="bg-blue-600 px-6 py-4 flex items-center justify-between text-white rounded-t-xl">
                            <div class="flex items-center gap-2">
                                <!-- Cash register icon SVG -->
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-lg font-black tracking-wide" id="modal-title">Record Payment</h3>
                            </div>
                            <button @click="closePayModal" class="text-white/80 hover:text-white transition" aria-label="Close modal">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <form :action="`/admin/order/pay/${orderId}`" method="POST" class="p-6 space-y-5">
                            @csrf
                            
                            <!-- Alert Info Box -->
                            <div class="bg-sky-50 border border-sky-100 rounded-lg p-4 flex items-start gap-3">
                                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs sm:text-sm font-semibold text-sky-800">
                                    Recording part payment for <span class="font-extrabold text-sky-950">Order #<span x-text="orderId"></span></span>. Current Balance: <span class="font-extrabold text-sky-950">PKR <span x-text="money(remainingAmount)"></span></span>
                                </div>
                            </div>

                            <!-- Payment Amount input group -->
                            <div>
                                <label for="payment_amount" class="block text-xs font-extrabold uppercase tracking-wide text-slate-500 mb-2">Payment Amount</label>
                                <div class="relative flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-4 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-sm font-bold text-slate-500">
                                        PKR
                                    </span>
                                    <input 
                                        type="number" 
                                        name="amount" 
                                        id="payment_amount" 
                                        x-model.number="paymentAmount"
                                        min="0.01" 
                                        :max="remainingAmount"
                                        step="0.01" 
                                        required
                                        class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-slate-300 text-sm font-extrabold text-slate-800 focus:border-blue-500 focus:ring-blue-500" 
                                    />
                                </div>
                            </div>

                            <!-- 2-Column Grid (Date and Method) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="payment_date" class="block text-xs font-extrabold uppercase tracking-wide text-slate-500 mb-2">Payment Date</label>
                                    <input 
                                        type="date" 
                                        name="payment_date" 
                                        id="payment_date" 
                                        x-model="paymentDate"
                                        required
                                        class="block w-full rounded-md border-slate-300 text-sm font-bold text-slate-800 focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-xs font-extrabold uppercase tracking-wide text-slate-500 mb-2">Payment Method</label>
                                    <select 
                                        name="payment_method" 
                                        id="payment_method" 
                                        x-model="paymentMethod"
                                        required
                                        class="block w-full rounded-md border-slate-300 text-sm font-bold text-slate-800 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Check">Check</option>
                                        <option value="EasyPaisa">EasyPaisa</option>
                                        <option value="JazzCash">JazzCash</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Notes textarea -->
                            <div>
                                <label for="notes" class="block text-xs font-extrabold uppercase tracking-wide text-slate-500 mb-2">Notes</label>
                                <textarea 
                                    name="notes" 
                                    id="notes" 
                                    rows="3" 
                                    x-model="notes"
                                    placeholder="Optional notes..." 
                                    class="block w-full rounded-md border-slate-300 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                                <button 
                                    type="button" 
                                    @click="closePayModal"
                                    class="px-5 py-2.5 rounded-md text-sm font-extrabold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-5 py-2.5 rounded-md text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition"
                                >
                                    Save Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function paymentModal() {
                    return {
                        isOpen: false,
                        orderId: '',
                        remainingAmount: 0,
                        paymentAmount: 0,
                        paymentDate: '{{ now()->format("Y-m-d") }}',
                        paymentMethod: 'Cash',
                        notes: '',
                        
                        openPayModal(id, amount) {
                            this.orderId = id;
                            this.remainingAmount = amount;
                            this.paymentAmount = amount;
                            this.paymentDate = '{{ now()->format("Y-m-d") }}';
                            this.paymentMethod = 'Cash';
                            this.notes = '';
                            this.isOpen = true;
                        },
                        
                        closePayModal() {
                            this.isOpen = false;
                        },
                        
                        money(value) {
                            return Number(value || 0).toFixed(2);
                        }
                    }
                }
            </script>
            
        </div>
    </div>

    <!-- Print styling -->
    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            header, footer, nav, aside, .print\:hidden, [role="navigation"] {
                display: none !important;
            }
            .min-h-\[calc\(100vh-4rem\)\] {
                min-height: auto !important;
                padding: 0 !important;
            }
        }
    </style>
</x-app-layout>
