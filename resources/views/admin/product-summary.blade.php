<x-app-layout>
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800 shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-blue-650 mb-4 md:mb-0">Profit & Inventory Summary</h1>
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- Refresh Stats -->
            <a href="{{ route('admin.product.summary') }}" class="bg-white hover:bg-zinc-50 text-blue-600 font-bold py-2.5 px-4 rounded-lg flex items-center shadow border border-blue-200 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 15.89M9 11l3-3 3 3m-3-3v12" /></svg>
                Refresh Stats
            </a>
            <!-- Export PDF -->
            <button onclick="window.print()" class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg flex items-center shadow border border-zinc-300 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-zinc-550" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Overall System Profitability Banner -->
    <div class="bg-blue-600 rounded-xl p-8 mb-8 text-center shadow-lg ring-1 ring-blue-500/20 text-white">
        <div class="text-xs font-bold uppercase tracking-widest text-blue-100/90 mb-2">Overall System Profitability</div>
        <div class="text-4xl md:text-5xl font-black">PKR {{ number_format($overallProfit, 2) }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-x-auto ring-1 ring-zinc-100 mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Total Bought</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sold</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Revenue</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Profit</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Last Sale</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($summaries as $summary)
                <tr class="hover:bg-zinc-50/70 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-extrabold text-blue-600 text-sm hover:underline cursor-pointer">{{ $summary->product_name ?? '-' }}</div>
                        <div class="text-xs font-semibold text-slate-400">{{ $summary->product_sku ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-semibold">{{ $summary->total_purchased }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-semibold">{{ $summary->total_sold }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-900">PKR {{ number_format($summary->total_revenue, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">PKR {{ number_format($summary->total_profit, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-emerald-700 text-white font-extrabold text-xs">
                            {{ $summary->current_stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                        {{ $summary->last_sale_date ? \Carbon\Carbon::parse($summary->last_sale_date)->format('M d, Y') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-zinc-400 font-semibold">No summary records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Box -->
    <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-5 flex items-start gap-4">
        <div class="p-2 rounded-full bg-cyan-100 text-cyan-600 mt-0.5 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <h4 class="font-extrabold text-slate-800 text-sm mb-1">How Profit is Calculated:</h4>
            <p class="text-xs text-slate-650 font-semibold leading-relaxed">
                Profit is based on the difference between the <strong class="text-slate-800 font-bold">Actual Cost</strong> at purchase and <strong class="text-slate-800 font-bold">Final Sale Price</strong> for each quantity sold.
            </p>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($summaries->hasPages())
        <div class="mt-6 border-t border-zinc-200 pt-4">
            {{ $summaries->links() }}
        </div>
    @endif
</div>
</x-app-layout>
