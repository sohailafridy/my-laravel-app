<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    @php
        $stats = [
            ['label' => 'Total Sales', 'value' => 'PKR ' . number_format($totalRevenue, 2), 'change' => 'Gross Revenue', 'tone' => 'text-zinc-950 bg-zinc-50 border-zinc-200', 'bar' => 'bg-zinc-950', 'width' => '100%'],
            ['label' => 'Dues Recovered', 'value' => 'PKR ' . number_format($recoveredDues, 2), 'change' => 'Collected cash', 'tone' => 'text-emerald-700 bg-emerald-50 border-emerald-200', 'bar' => 'bg-emerald-500', 'width' => $totalRevenue > 0 ? (($recoveredDues / $totalRevenue) * 100) . '%' : '0%'],
            ['label' => 'Outstanding Udhaar', 'value' => 'PKR ' . number_format($outstandingDues, 2), 'change' => 'Remaining Dues', 'tone' => 'text-rose-700 bg-rose-50 border-rose-200', 'bar' => 'bg-rose-500', 'width' => $totalRevenue > 0 ? (($outstandingDues / $totalRevenue) * 100) . '%' : '0%'],
            ['label' => 'Overhead Expenses', 'value' => 'PKR ' . number_format($totalExpenses, 2), 'change' => 'Total Spent', 'tone' => 'text-amber-700 bg-amber-50 border-amber-200', 'bar' => 'bg-amber-500', 'width' => $totalRevenue > 0 ? (min(100, ($totalExpenses / $totalRevenue) * 100)) . '%' : '0%'],
        ];
    @endphp
    <section class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-5">
                <!-- Top Real-Time Stats Grid -->
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($stats as $stat)
                        <article class="motion-card rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md" style="animation-delay: {{ $loop->index * 90 }}ms;">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-450">{{ $stat['label'] }}</p>
                                    <p class="mt-2 text-xl font-black text-zinc-950">{{ $stat['value'] }}</p>
                                </div>
                                <span class="{{ $stat['tone'] }} rounded-lg border px-2 py-0.5 text-[10px] font-bold">{{ $stat['change'] }}</span>
                            </div>
                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-zinc-100">
                                <div class="{{ $stat['bar'] }} motion-bar h-full rounded-full" style="width: {{ $stat['width'] }}; animation-delay: {{ 220 + ($loop->index * 120) }}ms;"></div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Monthly Revenue Chart Section -->
                <div class="motion-card rounded-xl border border-zinc-200 bg-white shadow-sm" style="animation-delay: 180ms;">
                    <div class="flex flex-col gap-3 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Sales Performance</h2>
                            <p class="text-xs font-semibold text-zinc-450">Dynamic performance summary over the past 12 months</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-zinc-950 px-3 py-1.5 text-xs font-bold text-white shadow-sm">Monthly Trend</span>
                        </div>
                    </div>

                    <div class="grid min-h-[320px] gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_220px]">
                        <!-- SVG / Bar Graph -->
                        <div class="flex min-h-[260px] items-end gap-3 rounded-xl border border-zinc-100 bg-zinc-50/50 p-4">
                            @foreach ($monthlySales as $mSale)
                                @php
                                    $heightPercent = $maxSales > 0 ? ($mSale['amount'] / $maxSales) * 100 : 0;
                                    $heightPercent = max(6, $heightPercent); // minimum height to make bar clickable/visible
                                @endphp
                                <div class="flex h-full flex-1 flex-col items-center justify-end">
                                    <div
                                        class="motion-bar w-full rounded-t-lg bg-zinc-900 transition duration-300 hover:bg-teal-500 relative group cursor-pointer"
                                        style="height: {{ $heightPercent }}%; animation-delay: {{ $loop->index * 30 }}ms;"
                                    >
                                        <!-- Interactive Tooltip on hover -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-zinc-950 text-white font-extrabold text-[10px] rounded-lg px-2.5 py-1.5 shadow-2xl z-10 whitespace-nowrap border border-zinc-800">
                                            {{ $mSale['name'] }}: PKR {{ number_format($mSale['amount'], 2) }}
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-bold text-zinc-400 mt-2">{{ $mSale['name'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Financial Ratios -->
                        <div class="grid gap-3">
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 flex flex-col justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Net Cash Flow / Profit</p>
                                    <p class="mt-2 text-lg font-black text-emerald-950">PKR {{ number_format($netProfit, 2) }}</p>
                                </div>
                            </div>
                            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 flex flex-col justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Active Customers</p>
                                    <p class="mt-2 text-lg font-black text-zinc-950">{{ number_format($activeCustomersCount) }} Profiles</p>
                                </div>
                            </div>
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 flex flex-col justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Dues Recovery Rate</p>
                                    @php
                                        $recoveryRate = $totalRevenue > 0 ? ($recoveredDues / $totalRevenue) * 100 : 0;
                                    @endphp
                                    <p class="mt-2 text-lg font-black text-amber-955">{{ number_format($recoveryRate, 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="motion-card rounded-xl border border-zinc-200 bg-white shadow-sm" style="animation-delay: 260ms;">
                    <div class="flex items-center justify-between border-b border-zinc-200 p-5">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Recent System Activity</h2>
                            <p class="text-xs font-semibold text-zinc-450">Latest logs of orders and recovered payments</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-left text-sm">
                            <thead class="bg-zinc-50 text-xs font-bold uppercase tracking-wider text-zinc-400">
                                <tr>
                                    <th class="px-5 py-3">Actor / Customer</th>
                                    <th class="px-5 py-3">Logged Activity</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Timeline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                @forelse ($activities as $activity)
                                    <tr class="transition hover:bg-zinc-50/50">
                                        <td class="whitespace-nowrap px-5 py-4 font-extrabold text-zinc-950">{{ $activity['name'] }}</td>
                                        <td class="px-5 py-4 text-xs font-semibold text-zinc-600">{{ $activity['event'] }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="rounded-lg px-2.5 py-0.5 text-[10px] font-bold border {{ $activity['status'] === 'Completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-amber-50 text-amber-700 border-amber-250' }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-xs font-bold text-zinc-400">{{ $activity['time'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-xs text-zinc-400 font-semibold">No recent transactions or logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar Panel: Low Stock & Quick Actions -->
            <aside class="space-y-5">
                <!-- Low Stock Alert Widget -->
                <section class="motion-card rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" style="animation-delay: 220ms;">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <div>
                            <h2 class="text-sm font-black text-zinc-950 flex items-center gap-1.5">
                                <svg class="h-4.5 w-4.5 text-rose-600 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                                </svg>
                                Low Stock Alert
                            </h2>
                            <p class="text-[10px] font-semibold text-zinc-400 mt-0.5">Critical products below threshold</p>
                        </div>
                        <span class="rounded-lg bg-rose-50 border border-rose-200 px-2 py-0.5 text-[10px] font-bold text-rose-700">
                            {{ $lowStockProducts->count() }} Items
                        </span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($lowStockProducts as $lowProd)
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-zinc-150 bg-zinc-50/50 hover:bg-zinc-50 transition">
                                <div>
                                    <div class="font-extrabold text-xs text-zinc-950">{{ $lowProd->name }}</div>
                                    <div class="text-[9px] font-semibold text-zinc-400">SKU: {{ $lowProd->sku ?? 'N/A' }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[9px]">
                                        {{ $lowProd->current_stock }} units left
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-zinc-400 font-semibold leading-relaxed">
                                🎉 Mubarak! Sab products ka stock behtareen level par hai.
                            </div>
                        @endforelse
                    </div>
                </section>

                <!-- Quick Actions Panel -->
                <section class="motion-card rounded-xl border border-zinc-250 bg-zinc-950 p-5 text-white shadow-sm" style="animation-delay: 300ms;">
                    <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                        <h2 class="text-sm font-extrabold">Quick Control Panel</h2>
                        <span class="rounded-lg bg-white/10 px-2 py-0.5 text-[10px] font-bold text-teal-200">Shortcuts</span>
                    </div>

                    <div class="mt-4 grid gap-3">
                        <a href="{{ route('admin.orders.create') }}" class="flex h-11 items-center justify-between rounded-lg bg-white px-3 text-xs font-bold text-zinc-950 transition hover:bg-teal-100 shadow">
                            Generate Order (Invoicing)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.expenses.index') }}" class="flex h-11 items-center justify-between rounded-lg bg-white/10 px-3 text-xs font-bold text-white transition hover:bg-white/15">
                            Log Operational Expense
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.customers.dues') }}" class="flex h-11 items-center justify-between rounded-lg bg-white/10 px-3 text-xs font-bold text-white transition hover:bg-white/15">
                            Outstanding Udhaar Recovery
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                            </svg>
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-app-layout>
