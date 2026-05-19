<x-app-layout>
<div class="container mx-auto px-4 py-6">
	<!-- Product Header -->
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
		<div>
			<h1 class="text-2xl font-extrabold text-blue-700 leading-tight">{{ $product->name ?? 'Product Name' }}</h1>
			<div class="text-sm text-gray-500 font-semibold mt-1">SKU: {{ $product->sku ?? '-' }} | Serial: #{{ $product->id ?? '-' }}</div>
		</div>
		<div class="flex gap-2 mt-4 md:mt-0">
			<a href="{{ route('admin.products', ['id' => $product->id ?? 0]) }}" class="inline-flex items-center px-4 py-2 border border-blue-500 text-blue-700 font-bold rounded-lg hover:bg-blue-50 transition">
				<svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>
				Edit Product
			</a>
			<a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition">
				<svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
				Print Ledger
			</a>
		</div>
	</div>

	<!-- Summary Cards -->
	<div class="mb-6 w-full overflow-x-auto">
		<div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; align-items: stretch; min-width: 980px;">

		<!-- TOTAL PURCHASES -->
		<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col">
			<div class="text-sm font-extrabold text-gray-600 uppercase tracking-tight">Total Purchases</div>
			<div class="flex items-center gap-4 mt-5 pb-4 border-b border-gray-200">
				<span class="inline-flex items-center justify-center bg-green-100 text-green-600 rounded-xl h-11 w-11 flex-shrink-0">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-6-6m6 6l6-6"/>
					</svg>
				</span>
				<div class="flex items-baseline gap-2">
					<span class="text-3xl font-extrabold leading-none text-gray-900">{{ $product_summary->total_purchased ?? 0 }}</span>
					<span class="text-3xl font-medium leading-none text-gray-500">Units</span>
				</div>
			</div>
			<div class="flex flex-col gap-2 mt-4 text-sm">
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Total Cost:</span>
					<span class="font-extrabold text-gray-900">PKR {{ number_format($product_summary->total_cost ?? 0, 2) }}</span>
				</div>
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Avg Buy Price:</span>
					<span class="font-extrabold text-gray-900">PKR {{ number_format($product->purchase_price ?? 0, 2) }}</span>
				</div>
			</div>
		</div>

		<!-- TOTAL SALES -->
		<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col">
			<div class="text-sm font-extrabold text-gray-600 uppercase tracking-tight">Total Sales</div>
			<div class="flex items-center gap-4 mt-5 pb-4 border-b border-gray-200">
				<span class="inline-flex items-center justify-center bg-blue-100 text-blue-500 rounded-xl h-11 w-11 flex-shrink-0">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 20V4m0 0l-6 6m6-6l6 6"/>
					</svg>
				</span>
				<div class="flex items-baseline gap-2">
					<span class="text-3xl font-extrabold leading-none text-gray-900">{{ $product_summary->total_sold ?? 0 }}</span>
					<span class="text-3xl font-medium leading-none text-gray-500">Units</span>
				</div>
			</div>
			<div class="flex flex-col gap-2 mt-4 text-sm">
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Total Revenue:</span>
					<span class="font-extrabold text-gray-900">PKR {{ number_format($product_summary->total_revenue ?? 0, 2) }}</span>
				</div>
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Avg Sale Price:</span>
					<span class="font-extrabold text-gray-900">PKR {{ number_format($product->sale_price ?? 0, 2) }}</span>
				</div>
			</div>
		</div>

		<!-- NET PROFIT SUMMARY -->
		<div class="rounded-2xl shadow-sm p-5 flex flex-col text-white" style="background: linear-gradient(135deg, #4f6ef7 0%, #3a4fd4 60%, #2d3db8 100%);">
			<div class="text-sm font-extrabold text-blue-200 uppercase tracking-tight">Net Profit Summary</div>
			<div class="text-4xl font-extrabold mt-6">PKR {{ number_format($product_summary->net_profit ?? 0, 2) }}</div>
			<div class="text-2xl font-extrabold mt-8">
				{{ $product_summary->profit_margin ?? 0 }}%
				<span class="text-base font-medium text-blue-100">Margin</span>
			</div>
			<div class="text-sm text-blue-100 font-semibold leading-relaxed mt-4 max-w-[220px]">
				Calculated from actual Buy Cost vs Final Sale Price per item.
			</div>
		</div>

		<!-- INVENTORY STATUS -->
		<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col">
			<div class="text-sm font-extrabold text-gray-600 uppercase tracking-tight">Inventory Status</div>
			<div class="flex items-center gap-4 mt-5 pb-4 border-b border-gray-200">
				<span class="inline-flex items-center justify-center bg-cyan-100 text-cyan-600 rounded-xl h-11 w-11 flex-shrink-0">
					<!-- Warehouse boxes icon -->
					<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
						<path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v1h1a1 1 0 011 1v10a1 1 0 01-1 1H3a1 1 0 01-1-1V6a1 1 0 011-1h1V4zm2 0v1h8V4H6zm-2 4v2h4V8H4zm6 0v2h4V8h-4zM4 12v2h4v-2H4zm6 0v2h4v-2h-4z"/>
					</svg>
				</span>
				<div class="flex items-baseline gap-2">
					<span class="text-3xl font-extrabold leading-none text-gray-900">{{ $summary['current_stock'] ?? 0 }}</span>
					<span class="text-3xl font-medium leading-none text-gray-500">In Stock</span>
				</div>
			</div>
			<div class="flex flex-col gap-2 mt-4 text-sm">
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Current Buy Rate:</span>
					<span class="font-extrabold text-gray-900">PKR {{ number_format($summary['current_buy_rate'] ?? 0, 2) }}</span>
				</div>
				<div class="flex items-center justify-between text-sm">
					<span class="text-gray-600 font-medium">Current Sale Rate:</span>
					<span class="font-extrabold text-blue-600">PKR {{ number_format($summary['current_sale_rate'] ?? 0, 2) }}</span>
				</div>
			</div>
		</div>

		</div>
	</div>

	<!-- Chronological Movement History -->
	<div class="bg-white rounded-lg shadow p-4 mt-6">
		<div class="flex items-center justify-between mb-2">
			<h2 class="text-lg font-extrabold text-gray-800">Chronological Movement History</h2>
			<span class="text-xs text-gray-500 font-bold">Last Activity: {{ $summary['last_activity'] ?? '-' }}</span>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full text-left">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Transaction Date</th>
						<th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Reference & Description</th>
						<th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Qty In/Out</th>
						<th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Entry Reference</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-100">
					@forelse($movements as $move)
					<tr>
						<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 flex items-center gap-2">
							<svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
							{{ \Carbon\Carbon::parse($move->date)->format('M d, Y | H:i') }}
						</td>
						<td class="px-4 py-4 whitespace-nowrap">
							<div class="font-extrabold text-black">{!! $move->reference !!}</div>
							<div class="text-xs {{ $move->type == 'out' ? 'text-blue-600' : 'text-green-600' }} font-bold">{{ $move->type == 'out' ? 'Stock Reduced' : 'Inventory Replenished' }}</div>
						</td>
						<td class="px-4 py-4 whitespace-nowrap text-lg font-extrabold {{ $move->type == 'out' ? 'text-red-500' : 'text-green-600' }}">
							{{ $move->type == 'out' ? '-' : '+' }}{{ $move->quantity }}
						</td>
						<td class="px-4 py-4 whitespace-nowrap">
							@if($move->type == 'out')
								<a href="#" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-100 transition text-xs">
									Invoice
									<svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
								</a>
							@else
								<a href="#" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-100 transition text-xs">
									Details
									<svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
								</a>
							@endif
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="4" class="px-4 py-6 text-center text-gray-400 font-bold">No movement history found.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>
</x-app-layout>
