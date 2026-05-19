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
        <h1 class="text-3xl font-extrabold text-blue-600 mb-4 md:mb-0">Inventory Movement Log</h1>
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.product.movement') }}" class="flex items-center bg-white rounded-lg shadow px-2 border border-gray-300">
                <input type="text" name="sku" placeholder="Search by Product/SKU..." class="px-4 py-2 focus:outline-none rounded-l-lg text-sm font-semibold text-zinc-700 placeholder:text-zinc-450" value="{{ $sku ?? '' }}">
                <button type="submit" class="p-2 text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 104 4a7 7 0 0013 13z" /></svg>
                </button>
            </form>
            <!-- Print Button -->
            <button onclick="window.print()" class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg flex items-center shadow border border-zinc-300 transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print History
            </button>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-x-auto ring-1 ring-zinc-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Reference</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($movements as $movement)
                <tr class="hover:bg-zinc-50/70 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">#{{ $movement->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-semibold">{{ \Carbon\Carbon::parse($movement->created_at)->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-extrabold text-gray-900 text-sm">{{ $movement->product_name ?? '-' }}</div>
                        <div class="text-xs font-semibold text-slate-400">{{ $movement->product_sku ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($movement->type === 'in')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded bg-emerald-100 text-emerald-700 font-bold text-xs uppercase tracking-wider">
                                ↓ Received
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded bg-red-100 text-red-700 font-bold text-xs uppercase tracking-wider">
                                ↑ Shipped
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-base font-black">
                        @if($movement->type === 'in')
                            <span class="text-emerald-600">+ {{ $movement->quantity }}</span>
                        @else
                            <span class="text-red-600">- {{ $movement->quantity }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600 hover:text-blue-800">
                        @if($movement->type === 'in')
                            <a href="{{ route('admin.purchases', ['sku' => $movement->product_sku]) }}">Purchase #{{ $movement->reference_id }}</a>
                        @else
                            <a href="#">Order #{{ $movement->reference_id }}</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-zinc-400 font-semibold">No stock movements found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($movements->hasPages())
        <div class="mt-6 border-t border-zinc-200 pt-4">
            {{ $movements->links() }}
        </div>
    @endif
</div>
</x-app-layout>
