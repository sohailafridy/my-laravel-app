<x-app-layout>
    <x-slot name="title">Outstanding Dues Recovery</x-slot>

    @php
        $grandTotalDues = $customers->sum('total_dues');
        $totalPendingCustomers = $customers->count();
        $totalPendingOrders = $customers->sum('pending_orders');
    @endphp

    <div class="container mx-auto px-4 py-8" x-data="{ 
        payModalOpen: false, 
        currentCustomer: { id: '', name: '', max_due: 0, route: '' },
        openPayModal(id, name, maxDue, route) {
            this.currentCustomer = { id: id, name: name, max_due: parseFloat(maxDue), route: route };
            this.payModalOpen = true;
        }
    }">
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

        @if(session('error') || $errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc pl-5 font-semibold text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-zinc-950">Dues & Recovery Ledger</h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">Central dashboard to monitor outstanding balances and record customer payments</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg flex items-center shadow border border-zinc-300 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Export Statement
                </button>
            </div>
        </div>

        <!-- Metrics cards -->
        <div class="grid gap-5 md:grid-cols-3 mb-8">
            <article class="motion-card rounded-xl border border-rose-200 bg-rose-50 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Total Outstanding Udhaar</p>
                        <p class="mt-2 text-3xl font-black text-rose-955">PKR {{ number_format($grandTotalDues, 2) }}</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-white shadow-sm border border-rose-200 text-rose-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="motion-card rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Pending Customers</p>
                        <p class="mt-2 text-3xl font-black text-amber-955">{{ number_format($totalPendingCustomers) }} Customers</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-white shadow-sm border border-amber-200 text-amber-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="motion-card rounded-xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Unpaid Invoices</p>
                        <p class="mt-2 text-3xl font-black text-blue-955">{{ number_format($totalPendingOrders) }} Orders</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-white shadow-sm border border-blue-200 text-blue-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
            </article>
        </div>

        <!-- Ledger Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden mb-8">
            <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
                <h3 class="font-extrabold text-zinc-900 text-base">Customers with Outstanding Dues</h3>
                <span class="text-xs font-bold bg-rose-100 text-rose-800 px-2.5 py-1 rounded-full">{{ $customers->count() }} Records Found</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Contact & Address</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Customer Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider text-center">Unpaid Orders</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider text-right">Outstanding Dues</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-zinc-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($customers as $cust)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.customer.ledger', $cust->id) }}" class="font-extrabold text-zinc-950 hover:underline cursor-pointer block text-sm">{{ $cust->name }}</a>
                                    <span class="text-xs font-semibold text-zinc-450">ID: {{ $cust->user_id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-zinc-650">
                                    <div class="text-zinc-800">{{ $cust->phone ?? 'N/A' }}</div>
                                    <div class="text-xs text-zinc-400 font-medium">{{ $cust->to_add }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($cust->customer_type)
                                        @case('distributor')
                                            <span class="inline-flex items-center rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-700 border border-teal-200">Distributor</span>
                                            @break
                                        @case('outside')
                                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-200">Outside</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 border border-zinc-200">Retail</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-zinc-800">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs">
                                        {{ $cust->pending_orders }} / {{ $cust->total_orders }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-rose-600">
                                    PKR {{ number_format($cust->total_dues, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        <button 
                                            @click="openPayModal('{{ $cust->id }}', '{{ addslashes($cust->name) }}', '{{ $cust->total_dues }}', '{{ route('admin.customers.pay-dues', $cust->id) }}')"
                                            class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-1.5 px-3 rounded-lg flex items-center gap-1 shadow-sm border border-rose-700 transition text-xs"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879-.659c1.171-.879 3.07-.879 4.242 0 1.172.879 1.172 2.303 0 3.182C13.536 21.371 12.36 21 12 21M9.01 9h5.98" />
                                            </svg>
                                            Collect Cash
                                        </button>
                                        <a href="{{ route('admin.customer.ledger', $cust->id) }}" class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-1.5 px-3 rounded-lg shadow-sm border border-zinc-200 transition text-xs">
                                            View Ledger
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-zinc-400 font-semibold">Mubarak Ho! Kisi customer ka koi udhaar baaqi nahi hai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Central Recovery Guide Box -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-6 flex items-start gap-4 text-white shadow-md">
            <div class="p-2.5 rounded-xl bg-zinc-800 text-teal-400 mt-0.5 shadow-sm border border-zinc-700 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h4 class="font-extrabold text-white text-base mb-1">Smart Recovery Ledger Allocation (First-In, First-Out):</h4>
                <p class="text-xs text-zinc-400 font-semibold leading-relaxed">
                    Jab aap "Collect Cash" ke zariye payment receive karenge, toh system automatically us amount ko is customer ke sabse purane pending order par apply karega. Agar payment order amount se zyaada hai, toh baaqi bacha hua paisa agle orders par distribute ho jayega jab tak ke poori payment adjust na ho jaye.
                </p>
            </div>
        </div>

        <!-- Collect Cash Modal (Alpine.js driven) -->
        <div 
            x-cloak
            x-show="payModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay background -->
                <div 
                    x-show="payModalOpen"
                    x-transition.opacity
                    class="fixed inset-0 bg-zinc-950/60 transition-opacity" 
                    @click="payModalOpen = false"
                ></div>

                <!-- Modal contents spacer -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Card -->
                <div 
                    x-show="payModalOpen"
                    x-transition.scale.95
                    class="inline-block align-middle bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-zinc-200"
                >
                    <div class="p-6 border-b border-zinc-150 bg-zinc-50 flex items-center justify-between">
                        <h3 class="text-lg font-extrabold text-zinc-950" id="modal-title">Collect Dues: <span x-text="currentCustomer.name"></span></h3>
                        <button @click="payModalOpen = false" class="text-zinc-400 hover:text-zinc-650 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="currentCustomer.route" method="POST" class="p-6 space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Customer Outstanding</label>
                            <div class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 font-extrabold text-sm flex items-center justify-between">
                                <span>Outstanding Udhaar:</span>
                                <span>PKR <span x-text="currentCustomer.max_due.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                            </div>
                        </div>

                        <div>
                            <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Payment Amount Received (PKR)</label>
                            <input 
                                type="number" 
                                name="amount" 
                                id="amount" 
                                step="0.01" 
                                min="0.01" 
                                :max="currentCustomer.max_due"
                                required 
                                class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-bold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                                :placeholder="'Enter amount up to ' + currentCustomer.max_due"
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="payment_date" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Payment Date</label>
                                <input 
                                    type="date" 
                                    name="payment_date" 
                                    id="payment_date" 
                                    required 
                                    value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                                >
                            </div>

                            <div>
                                <label for="payment_method" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Method</label>
                                <select 
                                    name="payment_method" 
                                    id="payment_method" 
                                    required 
                                    class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                                >
                                    <option value="Cash" selected>Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="EasyPaisa">EasyPaisa</option>
                                    <option value="JazzCash">JazzCash</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Remarks / Notes (Optional)</label>
                            <textarea 
                                name="notes" 
                                id="notes" 
                                rows="2" 
                                placeholder="E.g. Cash handed over by customer directly"
                                class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                            ></textarea>
                        </div>

                        <div class="pt-4 border-t border-zinc-150 flex items-center justify-end gap-3">
                            <button 
                                type="button" 
                                @click="payModalOpen = false" 
                                class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg shadow-sm border border-zinc-300 transition text-sm"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-5 rounded-lg shadow border border-rose-700 transition text-sm flex items-center gap-1.5"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Confirm & Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
