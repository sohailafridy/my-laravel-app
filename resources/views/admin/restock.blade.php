<x-app-layout>
	<x-slot name="title">Restock</x-slot>
	<div class="min-h-[calc(100vh-4rem)] bg-zinc-50 px-5 py-6">
		<div class="mx-auto max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-6">
			<!-- Restock Form -->
			<div class="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
				<h2 class="text-xl font-bold flex items-center gap-2">
					<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 0 1 2-2h2m0 0h10a2 2 0 0 1 2 2v2m-2-2v16m-8-16v16m-4-4h16"/></svg>
					Restock Form
				</h2>
                <form action="{{ route('admin.restock.store') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div x-data="restockForm()">
                        <!-- Product Select -->
                        <label class="font-semibold text-sm mb-1">SELECT PRODUCT</label>
                        <select x-model="selectedProductId" @change="fetchProductDetail" class="w-full border rounded px-3 py-2 mb-2" name="product_id">
                            <option value="">--- Search Product ---</option>
                            @foreach(\App\Models\Product::all() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku ?? '' }})</option>
                            @endforeach
                        </select>

                        <div class="flex gap-4 mb-2">
                            <div class="flex-1">
                                <label class="font-semibold text-sm mb-1">ADD QUANTITY</label>
                                <div class="flex items-center border rounded px-2 py-1">
                                    <button type="button" @click="quantity = Math.max(0, quantity-1)" class="px-2 text-lg">-</button>
                                    <input type="number" min="0" x-model="quantity" class="w-16 border-0 text-center focus:ring-0" name="quantity" />
                                    <button type="button" @click="quantity++" class="px-2 text-lg">+</button>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="font-semibold text-sm mb-1">PURCHASE COST</label>
                                <div class="flex items-center border rounded px-2 py-1">
                                    <span class="mr-2 text-sm">PKR</span>
                                    <input type="number" min="0" step="0.01" x-model="purchaseCost" class="w-full border-0 focus:ring-0" name="purchase_price" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="font-semibold text-sm mb-1">SALE PRICE</label>
                            <div class="flex items-center border rounded px-2 py-1">
                                <svg class="w-5 h-5 text-zinc-400 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17l9-9M7 7h.01M17 17h.01"/></svg>
                                <input type="number" min="0" step="0.01" x-model="salePrice" class="w-full border-0 focus:ring-0" name="sale_price" />
                            </div>
                            <span class="text-xs text-zinc-500">This will update the standard selling price for future orders.</span>
                        </div>o

                        <div class="bg-zinc-100 rounded p-3 flex justify-between items-center mb-2">
                            <span class="text-sm">Estimated Total Cost:</span>
                            <span class="font-bold">PKR <span x-text="(quantity * purchaseCost).toFixed(2)"></span></span>
                        </div>

                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition">Confirm & Add Stock</button>
                    </div>
                </form>
			</div>

			<!-- Product Detail Panel -->
			<div class="bg-blue-100 rounded-xl shadow p-6 min-h-[400px] flex flex-col justify-center items-center" x-data="restockForm()" x-init="initDetailPanel()">
				<template x-if="!productDetail">
					<div class="flex flex-col items-center justify-center h-full">
						<svg class="w-20 h-20 text-blue-300 mb-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
						<div class="font-bold text-lg text-blue-700 mb-2">No Product Selected</div>
						<div class="text-sm text-blue-700">Select a product on the left to see current stock levels and price history.</div>
					</div>
				</template>
				<template x-if="productDetail">
					<div class="w-full">
						<div class="text-xl font-bold text-blue-700 mb-1" x-text="productDetail.name + ' (SKU: ' + (productDetail.sku ?? '-') + ')' "></div>
						<div class="text-sm text-zinc-700 mb-4">Live Hardware Inventory Tracking</div>
						<div class="grid grid-cols-2 gap-4 mb-4">
							<div class="bg-white rounded p-3 text-center">
								<div class="text-xs text-zinc-500">In Stock</div>
								<div class="text-2xl font-extrabold text-blue-700" x-text="productDetail.current_stock ?? 0"></div>
							</div>
							<div class="bg-white rounded p-3 text-center">
								<div class="text-xs text-zinc-500">Profit/Unit</div>
								<div class="text-2xl font-extrabold text-green-600" x-text="'PKR ' + (productDetail.sale_price - productDetail.purchase_price).toFixed(2)"></div>
							</div>
						</div>
						<div class="bg-white rounded p-3 mb-2">
							<div class="flex justify-between text-sm mb-1">
								<span>Last Purchase Cost</span>
								<span class="font-bold">PKR <span x-text="productDetail.purchase_price.toFixed(2)"></span></span>
							</div>
							<div class="flex justify-between text-sm">
								<span>Current Sale Price</span>
								<span class="font-bold">PKR <span x-text="productDetail.sale_price.toFixed(2)"></span></span>
							</div>
						</div>
						<div class="bg-yellow-100 border-l-4 border-yellow-400 text-yellow-700 p-3 rounded mt-2 text-xs flex items-center gap-2">
							<svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/></svg>
							Restocking will immediately update overall profit and loss reports.
						</div>
					</div>
				</template>
			</div>
		</div>
	</div>

	<script>
	function restockForm() {
		return {
			selectedProductId: '',
			quantity: 0,
			purchaseCost: 100.00,
			salePrice: '',
			productDetail: null,
			fetchProductDetail() {
				if (!this.selectedProductId) {
					this.productDetail = null;
					return;
				}
				axios.get(`/api/product/${this.selectedProductId}`)
					.then(res => {
						this.productDetail = res.data;
						this.purchaseCost = res.data.purchase_price;
						this.salePrice = res.data.sale_price;
					})
					.catch(() => {
						this.productDetail = null;
					});
			},
			initDetailPanel() {
				// For right panel to share state with left, use Alpine store or window event in real app
			}
		}
	}
	</script>
</x-app-layout>
