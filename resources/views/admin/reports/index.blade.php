<x-app-layout>
    <x-slot name="title">Business Performance Reports</x-slot>

    <!-- Custom styling for motion effects, hover transitions, and print layout -->
    <style>
        .motion-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .motion-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.15);
        }
        .active-tab {
            border-color: #0f172a;
            color: #0f172a;
        }
        @media print {
            aside, nav, header, form, button, .no-print {
                display: none !important;
            }
            body, .container, main {
                background: white !important;
                color: black !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-only {
                display: block !important;
            }
            .print-card {
                border: 1px solid #e4e4e7 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>

    @php
        // Prepare asc order arrays for Chart.js
        $chartData = collect($reportRows)->reverse();
        $chartLabels = $chartData->map(function($r) use ($interval) {
            try {
                if ($interval === 'monthly') {
                    return \Carbon\Carbon::parse($r->period . '-01')->format('M Y');
                } elseif ($interval === 'daily') {
                    return \Carbon\Carbon::parse($r->period)->format('d M');
                } elseif ($interval === 'weekly') {
                    return 'Wk of ' . \Carbon\Carbon::parse($r->period)->format('d M');
                }
            } catch(\Exception $e) {}
            return $r->period;
        })->values()->toArray();

        $chartSales = $chartData->pluck('sales')->values()->toArray();
        $chartExpenses = $chartData->pluck('expenses')->values()->toArray();
        $chartProfit = $chartData->pluck('net_profit')->values()->toArray();
    @endphp

    <div class="container mx-auto px-4 py-8" x-data="{ currentTab: '{{ $metric === 'sales' ? 'sales' : ($metric === 'expense' ? 'expenses' : ($metric === 'profit' ? 'profit' : 'consolidated')) }}', selectedInterval: '{{ $interval ?? '' }}' }">
        
        <!-- Top Title Bar -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-zinc-950 flex items-center gap-3">
                    Business Analytics & Reports
                </h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">
                    Analyze sales revenue, COGS, operational expenses, and profitability metrics.
                </p>
            </div>
            
            <div class="flex items-center gap-3 no-print">
                <button 
                    onclick="window.print()" 
                    class="bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-300 font-bold py-2.5 px-4 rounded-lg flex items-center shadow-sm transition text-sm gap-2"
                >
                    <svg class="h-4.5 w-4.5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.6 0-1.08-.474-1.12-1.078L5.82 18m11.84 0H5.82m11.84 0a3.213 3.213 0 0 0 3.102-2.433L21.496 12a3.375 3.375 0 0 0-3.248-4.249H19.5v-3.75A2.25 2.25 0 0 0 17.25 1.875h-10.5A2.25 2.25 0 0 0 4.5 4.125v3.75H4.252a3.375 3.375 0 0 0-3.248 4.249l.522 3.568a3.203 3.203 0 0 0 3.098 2.433H5.82M16.5 7.5h-9L7.5 4.125A.375.375 0 0 1 7.875 3.75h8.25a.375.375 0 0 1 .375.375V7.5Z" />
                    </svg>
                    Print Report
                </button>
            </div>
        </div>

        <!-- Print-Only Header Details -->
        <div class="hidden print-only mb-8 border-b border-zinc-200 pb-4">
            <h2 class="text-2xl font-black text-zinc-950">PERFORMANCE SUMMARY REPORT</h2>
            <div class="grid grid-cols-2 gap-4 mt-4 text-xs font-semibold text-zinc-650">
                <div>
                    <span class="text-zinc-400">Report Interval:</span> <span class="capitalize text-zinc-800">{{ $interval }}</span>
                </div>
                <div>
                    <span class="text-zinc-400">Date Range:</span> 
                    <span class="text-zinc-800">
                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M, Y') : 'Beginning' }} 
                        to 
                        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M, Y') : 'Today' }}
                    </span>
                </div>
                @if(!empty($search))
                    <div>
                        <span class="text-zinc-400">Search Filter:</span> <span class="text-zinc-800">"{{ $search }}"</span>
                    </div>
                @endif
                <div>
                    <span class="text-zinc-400">Printed On:</span> <span class="text-zinc-800">{{ now()->format('d M, Y H:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mb-8 no-print">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="space-y-6">
                <!-- Search text and basic configs -->
                <div class="grid gap-5 md:grid-cols-4">
                    
                    <!-- Search Input -->
                    <div class="md:col-span-1">
                        <label for="search" class="block text-xs font-extrabold uppercase tracking-wider text-zinc-400 mb-2">Search Query</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-zinc-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                value="{{ $search }}" 
                                placeholder="Order ID, customer, detail..."
                                class="pl-9 w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950 transition"
                            >
                        </div>
                    </div>

                    <!-- Time Interval Filter -->
                    <div>
                        <label for="interval" class="block text-xs font-extrabold uppercase tracking-wider text-zinc-400 mb-2">Time Interval</label>
                        <select 
                            name="interval" 
                            id="interval" 
                            x-model="selectedInterval"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-bold text-zinc-800 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950 transition cursor-pointer"
                        >
                            <option value="" {{ empty($interval) ? 'selected' : '' }}>Custom Date Range</option>
                            <option value="daily" {{ $interval === 'daily' ? 'selected' : '' }}>Daily Aggregations</option>
                            <option value="weekly" {{ $interval === 'weekly' ? 'selected' : '' }}>Weekly Aggregations</option>
                            <option value="monthly" {{ $interval === 'monthly' ? 'selected' : '' }}>Monthly Aggregations</option>
                            <option value="yearly" {{ $interval === 'yearly' ? 'selected' : '' }}>Yearly Aggregations</option>
                        </select>
                    </div>

                    <!-- Custom Start Date -->
                    <div>
                        <label for="start_date" class="block text-xs font-extrabold uppercase tracking-wider text-zinc-400 mb-2">Start Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            value="{{ $startDate }}"
                            :disabled="selectedInterval !== ''"
                            :class="selectedInterval !== '' ? 'opacity-50 bg-zinc-100 cursor-not-allowed' : ''"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950 transition"
                        >
                    </div>

                    <!-- Custom End Date -->
                    <div>
                        <label for="end_date" class="block text-xs font-extrabold uppercase tracking-wider text-zinc-400 mb-2">End Date</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            value="{{ $endDate }}"
                            :disabled="selectedInterval !== ''"
                            :class="selectedInterval !== '' ? 'opacity-50 bg-zinc-100 cursor-not-allowed' : ''"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950 transition"
                        >
                    </div>
                </div>

                <!-- Metrics Segmented Pill selectors and submission controls -->
                <div class="border-t border-zinc-150 pt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <!-- Segmented Control Button Pill (Urdu: Sales, Profit, Expense check segment) -->
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 mb-2">Drill-Down Focus Metric</label>
                        <div class="inline-flex rounded-lg bg-zinc-100 p-1 border border-zinc-200">
                            <input type="radio" name="metric" id="m_all" value="all" class="sr-only" {{ $metric === 'all' ? 'checked' : '' }}>
                            <label for="m_all" class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition {{ $metric === 'all' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900' }}">
                                All Metrics
                            </label>

                            <input type="radio" name="metric" id="m_sales" value="sales" class="sr-only" {{ $metric === 'sales' ? 'checked' : '' }}>
                            <label for="m_sales" class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition {{ $metric === 'sales' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900' }}">
                                Sales (Revenue)
                            </label>

                            <input type="radio" name="metric" id="m_profit" value="profit" class="sr-only" {{ $metric === 'profit' ? 'checked' : '' }}>
                            <label for="m_profit" class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition {{ $metric === 'profit' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900' }}">
                                Profit Margin
                            </label>

                            <input type="radio" name="metric" id="m_expense" value="expense" class="sr-only" {{ $metric === 'expense' ? 'checked' : '' }}>
                            <label for="m_expense" class="cursor-pointer px-4 py-1.5 rounded-md text-xs font-bold transition {{ $metric === 'expense' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900' }}">
                                Expenses
                            </label>
                        </div>
                    </div>

                    <!-- Search / Clear buttons -->
                    <div class="flex items-center gap-3 self-end">
                        <a 
                            href="{{ route('admin.reports.index') }}" 
                            class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg text-xs shadow-sm border border-zinc-300 transition text-center"
                        >
                            Reset Filters
                        </a>
                        <button 
                            type="submit" 
                            class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold py-2.5 px-6 rounded-lg text-xs shadow transition flex items-center gap-1.5 justify-center"
                        >
                            <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Fetch Analysis
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Premium KPI Metrics Dashboard Cards -->
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            
            <!-- Card 1: Total Revenue (Sales) -->
            <article class="motion-card print-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Total Sales Revenue</p>
                        <p class="mt-2 text-2xl font-black text-zinc-950">PKR {{ number_format($totalSales, 2) }}</p>
                        <p class="text-[10px] font-semibold text-zinc-450 mt-1 flex items-center gap-1">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            {{ $totalOrdersCount }} successful orders
                        </p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h.007m-.007-.007H3.75m.007 0a.75.75 0 1 1-.007.007M4.75 5.25h.008M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
            </article>

            <!-- Card 2: Cost of Goods Sold (COGS) -->
            <article class="motion-card print-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Cost of Goods (COGS)</p>
                        <p class="mt-2 text-2xl font-black text-zinc-950">PKR {{ number_format($totalCogs, 2) }}</p>
                        <p class="text-[10px] font-semibold text-zinc-450 mt-1">
                            Purchase value of inventory sold
                        </p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                </div>
            </article>

            <!-- Card 3: Total Expenses (Overhead) -->
            <article class="motion-card print-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Operational Expenses</p>
                        <p class="mt-2 text-2xl font-black text-zinc-950">PKR {{ number_format($totalExpenses, 2) }}</p>
                        <p class="text-[10px] font-semibold text-zinc-450 mt-1 flex items-center gap-1">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                            {{ $totalExpensesCount }} transactions logged
                        </p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879-.659c1.171-.879 3.07-.879 4.242 0 1.172.879 1.172 2.303 0 3.182C13.536 21.371 12.36 21 12 21M9.01 9h5.98" />
                        </svg>
                    </span>
                </div>
            </article>

            <!-- Card 4: Net Profit (Bottom line) -->
            <article class="motion-card print-card rounded-xl border border-zinc-200 bg-zinc-900 p-6 shadow-sm text-white relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10 text-white">
                    <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400">Net Business Profit</p>
                        <p class="mt-2 text-2xl font-black text-white">PKR {{ number_format($totalNetProfit, 2) }}</p>
                        @php
                            $profitMargin = $totalSales > 0 ? ($totalNetProfit / $totalSales) * 100 : 0;
                        @endphp
                        <p class="text-[10px] font-bold text-teal-300 mt-1">
                            Net Profit Margin: {{ number_format($profitMargin, 1) }}%
                        </p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </span>
                </div>
            </article>
        </div>

        <!-- Financial Performance Trend Chart (Chart.js Panel) -->
        @if(count($reportRows) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mb-8 print-card">
                <h3 class="font-extrabold text-zinc-950 text-base mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    Performance Trend Overview
                </h3>
                
                <div class="relative w-full h-[320px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        @endif

        <!-- Details Tab Bar (Consolidated vs Granular Tables) no-print -->
        <div class="flex border-b border-zinc-200 mb-6 gap-6 no-print">
            <button 
                @click="currentTab = 'consolidated'"
                :class="currentTab === 'consolidated' ? 'active-tab font-extrabold border-b-2' : 'text-zinc-500 font-semibold'"
                class="pb-3 text-sm transition focus:outline-none flex items-center gap-1.5"
            >
                Consolidated Summary
                <span class="text-[10px] bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded-full font-bold">
                    {{ count($reportRows) }}
                </span>
            </button>
            
            @if($metric === 'all' || $metric === 'sales' || $metric === 'profit')
                <button 
                    @click="currentTab = 'sales'"
                    :class="currentTab === 'sales' ? 'active-tab font-extrabold border-b-2' : 'text-zinc-500 font-semibold'"
                    class="pb-3 text-sm transition focus:outline-none flex items-center gap-1.5"
                >
                    Sales Transactions (Orders)
                    <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-bold">
                        {{ $ordersDetail->count() }}
                    </span>
                </button>
            @endif

            @if($metric === 'all' || $metric === 'expense')
                <button 
                    @click="currentTab = 'expenses'"
                    :class="currentTab === 'expenses' ? 'active-tab font-extrabold border-b-2' : 'text-zinc-500 font-semibold'"
                    class="pb-3 text-sm transition focus:outline-none flex items-center gap-1.5"
                >
                    Overhead Expense Logs
                    <span class="text-[10px] bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full font-bold">
                        {{ $expensesDetail->count() }}
                    </span>
                </button>
            @endif
        </div>

        <!-- ==============================================
             TAB CONTENT 1: CONSOLIDATED PERIOD SUMMARY TABLE
             ============================================== -->
        <div x-show="currentTab === 'consolidated'" class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden print-card">
                <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between no-print">
                    <h3 class="font-extrabold text-zinc-950 text-base">Consolidated Statement by Period</h3>
                    <span class="text-xs font-bold bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-full capitalize">
                        Interval: {{ $interval }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Sales Revenue</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider text-amber-500">COGS</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Gross Profit</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider text-rose-500">Expenses</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Net Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @forelse ($reportRows as $row)
                                <tr class="hover:bg-zinc-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-950">
                                        @try {
                                            if ($interval === 'monthly') {
                                                echo \Carbon\Carbon::parse($row->period . '-01')->format('F Y');
                                            } elseif ($interval === 'daily') {
                                                echo \Carbon\Carbon::parse($row->period)->format('d M, Y');
                                            } elseif ($interval === 'weekly') {
                                                echo 'Week of ' . \Carbon\Carbon::parse($row->period)->format('d M, Y');
                                            } else {
                                                echo $row->period;
                                            }
                                        } catch (\Exception $e) {
                                            echo $row->period;
                                        }
                                    @endphp
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-zinc-700">
                                        PKR {{ number_format($row->sales, 2) }}
                                        <span class="block text-[10px] text-zinc-400 font-medium">{{ $row->orders_count }} sales</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-zinc-500">
                                        PKR {{ number_format($row->cogs, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-teal-600">
                                        PKR {{ number_format($row->gross_profit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-rose-600">
                                        PKR {{ number_format($row->expenses, 2) }}
                                        <span class="block text-[10px] text-zinc-400 font-medium">{{ $row->expenses_count }} items</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black {{ $row->net_profit >= 0 ? 'text-teal-700' : 'text-rose-700' }}">
                                        PKR {{ number_format($row->net_profit, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-zinc-400 font-semibold">
                                        No aggregated financial records found for the selected parameters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==============================================
             TAB CONTENT 2: GRANULAR SALES TRANSACTIONS
             ============================================== -->
        @if($metric === 'all' || $metric === 'sales' || $metric === 'profit')
            <div x-show="currentTab === 'sales'" class="space-y-6 no-print">
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
                    <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
                        <h3 class="font-extrabold text-zinc-950 text-base">Sales Drill-down Transaction Log</h3>
                        <span class="text-xs font-bold bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-full">
                            Last 100 matches
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200">
                            <thead class="bg-zinc-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Items Summary</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Final Value</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Remaining Dues</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-zinc-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                @forelse ($ordersDetail as $order)
                                    <tr class="hover:bg-zinc-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-950">
                                            #{{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-800">
                                            {{ $order->created_at->format('d M, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-zinc-700">
                                            {{ $order->customer->name ?? 'Guest' }}
                                            <span class="block text-[10px] text-zinc-400 font-semibold">{{ $order->customer->phone ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-zinc-500 max-w-[280px]">
                                            @php
                                                $itemStrings = $order->items->map(function($item) {
                                                    return ($item->product->name ?? 'Unknown') . ' (x' . $item->quantity . ')';
                                                })->toArray();
                                                echo implode(', ', $itemStrings);
                                            @endphp
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-zinc-800">
                                            PKR {{ number_format($order->final_amount, 2) }}
                                            <span class="block text-[10px] text-zinc-400 font-semibold">Disc: {{ number_format($order->discount, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold {{ $order->remaining_amount > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                            PKR {{ number_format($order->remaining_amount, 2) }}
                                            <span class="block text-[10px] font-bold">{{ $order->remaining_amount > 0 ? 'Dues Pending' : 'Paid in Full' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a 
                                                href="{{ route('order.invoice', $order->id) }}" 
                                                class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:text-teal-700 transition"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                                Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center text-zinc-400 font-semibold">
                                            No detailed orders matches found in database for the chosen timeframe.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- ==============================================
             TAB CONTENT 3: GRANULAR EXPENSE TRANSACTIONS
             ============================================== -->
        @if($metric === 'all' || $metric === 'expense')
            <div x-show="currentTab === 'expenses'" class="space-y-6 no-print">
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
                    <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
                        <h3 class="font-extrabold text-zinc-950 text-base">Overhead Expenses Drill-down Log</h3>
                        <span class="text-xs font-bold bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-full">
                            Last 100 matches
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200">
                            <thead class="bg-zinc-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Description / Notes</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-zinc-400 uppercase tracking-wider">Receipt</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                @forelse ($expensesDetail as $exp)
                                    <tr class="hover:bg-zinc-50/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-800">
                                            {{ \Carbon\Carbon::parse($exp->date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 border border-zinc-200">
                                                {{ $exp->category_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-zinc-650 min-w-[280px]">
                                            {{ $exp->detail }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            @if (!empty($exp->file))
                                                <a 
                                                    href="{{ asset('storage/' . $exp->file) }}" 
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:text-teal-700 transition"
                                                >
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View File
                                                </a>
                                            @else
                                                <span class="text-zinc-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-rose-600">
                                            PKR {{ number_format($exp->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center text-zinc-400 font-semibold">
                                            No operational expense transactions found matching parameters in the timeline.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Integrate Chart.js dynamically from CDN for premium visual performance trend graphs -->
    @if(count($reportRows) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('trendChart').getContext('2d');
                
                const labels = @json($chartLabels);
                const salesData = @json($chartSales);
                const expensesData = @json($chartExpenses);
                const profitData = @json($chartProfit);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Sales (Revenue)',
                                data: salesData,
                                backgroundColor: 'rgba(16, 185, 129, 0.65)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderRadius: 6,
                                borderWidth: 1,
                                type: 'bar'
                            },
                            {
                                label: 'Expenses (Overhead)',
                                data: expensesData,
                                backgroundColor: 'rgba(239, 68, 68, 0.65)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderRadius: 6,
                                borderWidth: 1,
                                type: 'bar'
                            },
                            {
                                label: 'Net Profit',
                                data: profitData,
                                backgroundColor: 'rgba(20, 184, 166, 0.15)',
                                borderColor: 'rgb(20, 184, 166)',
                                borderWidth: 3.5,
                                pointBackgroundColor: 'rgb(20, 184, 166)',
                                pointHoverRadius: 7,
                                tension: 0.35,
                                type: 'line'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(228, 228, 231, 0.55)',
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'PKR ' + value.toLocaleString();
                                    },
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        weight: 'bold',
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                padding: 12,
                                titleFont: {
                                    weight: 'black',
                                    size: 13
                                },
                                bodyFont: {
                                    weight: 'bold',
                                    size: 12
                                },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += 'PKR ' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
