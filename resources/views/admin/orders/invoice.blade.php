<x-app-layout>
    <x-slot name="title">Order Invoice #{{ $order->id }}</x-slot>

    <!-- Google Fonts & Icon Styles -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;950&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        .invoice-card {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif !important;
        }
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
            .invoice-wrapper {
                padding: 0 !important;
                background: white !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>

    <div class="invoice-wrapper min-h-[calc(100vh-4rem)] bg-zinc-50 py-8 px-4 sm:px-6 lg:px-8 print:bg-white print:py-0 print:px-0">
        <!-- Control panel for screen only -->
        <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between print:hidden">
            <a href="{{ route('admin.orders.list') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>

            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
        </div>

        <!-- Main Invoice Container -->
        <div class="invoice-card max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-zinc-200/80 p-8 sm:p-12 print:shadow-none print:border-0 print:p-0">
            
            <!-- Header Section -->
            <div class="flex justify-between items-start pb-6 border-b border-zinc-200/80">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 sm:text-3xl">Order Details #{{ $order->id }}</h1>
                    <p class="text-xs font-semibold text-zinc-500 mt-1.5">{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y H:i') }}</p>
                </div>
                <div>
                    <h2 class="text-xl font-black text-blue-600 tracking-tight">Zamzam2 Inc.</h2>
                </div>
            </div>

            <!-- Customer Details & Payment Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-6 border-b border-zinc-200/80">
                <div>
                    <h3 class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">CUSTOMER INFO</h3>
                    @if ($order->customer)
                        <div class="space-y-1 text-sm text-zinc-700">
                            <p class="text-base font-extrabold text-zinc-900 mb-1.5">{{ $order->customer->name }}</p>
                            @if($order->customer->email)
                                <p class="text-zinc-600">{{ $order->customer->email }}</p>
                            @endif
                            @if($order->customer->phone)
                                <p class="text-zinc-600">{{ $order->customer->phone }}</p>
                            @endif
                            @if($order->customer->cnic)
                                <p class="text-zinc-600"><span class="font-extrabold text-zinc-800">CNIC:</span> {{ $order->customer->cnic }}</p>
                            @endif
                            @if($order->customer->from_add)
                                <p class="text-zinc-600"><span class="font-extrabold text-zinc-800">From:</span> {{ $order->customer->from_add }}</p>
                            @endif
                            @if($order->customer->to_add)
                                <p class="text-zinc-600"><span class="font-extrabold text-zinc-800">To:</span> {{ $order->customer->to_add }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-zinc-500 italic">Walk-in Customer / Outside Customer (Record not saved)</p>
                    @endif
                </div>

                <div class="md:text-right flex flex-col md:items-end justify-start">
                    <h3 class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">PAYMENT STATUS</h3>
                    <div>
                        @if ($order->remaining_amount <= 0)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 uppercase tracking-wider">
                                Paid
                            </span>
                        @elseif ($order->paid_amount > 0)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100 uppercase tracking-wider">
                                Partially Paid
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100 uppercase tracking-wider">
                                Unpaid
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="py-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-200/80 text-[11px] font-bold text-zinc-400 uppercase tracking-widest">
                                <th class="py-3 w-7/12">PRODUCT</th>
                                <th class="py-3 text-center w-1/12">QTY</th>
                                <th class="py-3 text-right w-2/12">UNIT PRICE</th>
                                <th class="py-3 text-right w-2/12">TOTAL PRICE</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="py-4">
                                        <div class="font-extrabold text-zinc-900 text-sm sm:text-base">{{ $item->product?->name ?? 'Unknown Product' }}</div>
                                        @if ($item->product?->sku)
                                            <div class="text-xs font-semibold text-zinc-400 mt-0.5">{{ $item->product->sku }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center text-sm font-bold text-zinc-700">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 text-right text-sm font-semibold text-zinc-500">
                                        PKR {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="py-4 text-right text-base font-extrabold text-zinc-900">
                                        PKR {{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Calculations Section -->
            <div class="flex justify-end py-6 border-t border-zinc-200/80">
                <div class="w-full sm:w-80 space-y-2.5">
                    <div class="flex justify-between text-sm font-semibold text-zinc-500">
                        <span>Subtotal</span>
                        <span class="text-zinc-900 font-extrabold">PKR {{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm font-semibold text-rose-600">
                            <span>Discount</span>
                            <span class="font-extrabold">-PKR {{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-sm font-extrabold text-zinc-900 border-t border-zinc-100 pt-2.5">
                        <span>Total</span>
                        <span class="text-lg text-blue-600 font-black">PKR {{ number_format($order->final_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-sm font-bold text-emerald-800 bg-emerald-50 px-3.5 py-2 rounded-lg border border-emerald-100 print:bg-white print:border-0 print:p-0 print:text-zinc-900">
                        <span>Amount Paid</span>
                        <span class="text-base text-emerald-700 font-black print:text-zinc-900">PKR {{ number_format($order->paid_amount, 2) }}</span>
                    </div>

                    @if($order->remaining_amount > 0)
                        <div class="flex justify-between items-center text-sm font-bold text-zinc-700 bg-zinc-50 px-3.5 py-2 rounded-lg border border-zinc-100 print:bg-white print:border-0 print:p-0">
                            <span>Remaining Balance</span>
                            <span class="text-base text-zinc-900 font-black">PKR {{ number_format($order->remaining_amount, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Logs Section (Only if payments have been recorded) -->
            @if(isset($payments) && $payments->count() > 0)
                <div class="mt-8 pt-8 border-t border-zinc-200/80">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-5">
                        <div class="flex items-center gap-2">
                            <!-- Clock/History icon -->
                            <svg class="w-5 h-5 text-zinc-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-sm font-black text-zinc-800 uppercase tracking-widest">Payment Logs</h3>
                        </div>
                        
                        <!-- Opening Due Pill -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-800 rounded-lg text-xs font-bold border border-amber-100 whitespace-nowrap">
                            <!-- Hand holding coin/money icon -->
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Opening Due: PKR {{ number_format($order->remaining_amount_fixed, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payments list table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-zinc-150 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                    <th class="py-2.5 w-1/3">DATE</th>
                                    <th class="py-2.5 w-1/3">METHOD</th>
                                    <th class="py-2.5 text-right w-1/3">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm font-semibold text-zinc-700">
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="py-3 text-zinc-500">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                        </td>
                                        <td class="py-3 text-zinc-800 capitalize">
                                            {{ $payment->payment_method ?? 'Cash' }}
                                        </td>
                                        <td class="py-3 text-right text-zinc-900 font-extrabold">
                                            PKR {{ number_format($payment->amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Recovered Row -->
                    <div class="mt-4 border-t border-zinc-200 pt-4 flex justify-between items-center">
                        <span class="text-xs font-black text-zinc-500 uppercase tracking-widest">Total Recovered</span>
                        <span class="text-base text-emerald-600 font-black">PKR {{ number_format($payments->sum('amount'), 2) }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
