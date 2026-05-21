<x-app-layout>
    <x-slot name="title">Expense Ledger Manager</x-slot>

    <div class="container mx-auto px-4 py-8" x-data="{ logFormOpen: false }">
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

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Verification failed:</span>
                </div>
                <ul class="list-disc pl-5 font-semibold text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-zinc-950">Expense Ledger</h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">Track and manage business overheads, operational expenditures, and logs</p>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    @click="logFormOpen = !logFormOpen"
                    class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold py-2.5 px-4 rounded-lg flex items-center shadow transition text-sm gap-2"
                >
                    <svg class="h-5 w-5 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Log New Expense
                </button>
            </div>
        </div>

        <!-- MoM Statistics & Overview Cards -->
        <div class="grid gap-5 md:grid-cols-3 mb-8">
            <article class="motion-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Filtered Total Spent</p>
                        <p class="mt-2 text-3xl font-black text-zinc-950">PKR {{ number_format($totalSpent, 2) }}</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h6m0 0v6m0-6L10 16l-4-4-4 4" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="motion-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">MoM Growth Rate</p>
                        @php
                            $diff = $totalSpent - $prevMonthSpent;
                            $percentage = $prevMonthSpent > 0 ? ($diff / $prevMonthSpent) * 100 : 0;
                        @endphp
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-3xl font-black text-zinc-950">
                                {{ number_format(abs($percentage), 1) }}%
                            </span>
                            @if($percentage > 0)
                                <span class="rounded-lg bg-rose-50 border border-rose-200 px-2 py-0.5 text-xs font-bold text-rose-700 flex items-center gap-0.5">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>
                                    Up
                                </span>
                            @elseif($percentage < 0)
                                <span class="rounded-lg bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-xs font-bold text-emerald-700 flex items-center gap-0.5">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" /></svg>
                                    Down
                                </span>
                            @else
                                <span class="rounded-lg bg-zinc-100 border border-zinc-200 px-2 py-0.5 text-xs font-bold text-zinc-700">Flat</span>
                            @endif
                        </div>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-zinc-50 text-zinc-500 border border-zinc-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                </div>
            </article>

            <article class="motion-card rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Categories Logged</p>
                        <p class="mt-2 text-3xl font-black text-zinc-950">{{ $categories->count() }} Categories</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                </div>
            </article>
        </div>

        <!-- Log Expense Collapsible Form Panel -->
        <div 
            x-cloak
            x-show="logFormOpen"
            x-collapse
            class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden mb-8"
        >
            <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
                <h3 class="font-extrabold text-zinc-950 text-base">Record Operational Expense</h3>
                <button @click="logFormOpen = false" class="text-zinc-400 hover:text-zinc-700 transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label for="expense_type_id" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Category *</label>
                        <select 
                            name="expense_type_id" 
                            id="expense_type_id" 
                            required 
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->exp_type_id }}">{{ $cat->expense_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Amount (PKR) *</label>
                        <input 
                            type="number" 
                            name="amount" 
                            id="amount" 
                            step="0.01" 
                            min="0.01" 
                            required 
                            placeholder="Enter amount paid"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-bold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                    </div>

                    <div>
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Transaction Date *</label>
                        <input 
                            type="date" 
                            name="date" 
                            id="date" 
                            required 
                            value="{{ date('Y-m-d') }}"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="detail" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Description / Detail *</label>
                        <textarea 
                            name="detail" 
                            id="detail" 
                            rows="3" 
                            required 
                            placeholder="E.g. Electricity bill for main showroom - May 2026"
                            class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-semibold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Receipt / Invoice Attachment (Optional)</label>
                        <div class="relative border-2 border-dashed border-zinc-250 hover:border-zinc-400 rounded-lg p-5 flex flex-col items-center justify-center transition bg-zinc-50/50">
                            <svg class="h-8 w-8 text-zinc-450 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-bold text-zinc-700">Choose Image or PDF Document</span>
                            <span class="text-[10px] text-zinc-450 mt-1 font-semibold">Max File Size: 5MB</span>
                            <input 
                                type="file" 
                                name="file" 
                                class="absolute inset-0 opacity-0 cursor-pointer"
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-150 flex items-center justify-end gap-3 bg-zinc-50/50 -mx-6 -mb-6 p-6">
                    <button 
                        type="button" 
                        @click="logFormOpen = false"
                        class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg shadow-sm border border-zinc-300 transition text-sm"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold py-2.5 px-5 rounded-lg shadow transition text-sm flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Log Expense Entry
                    </button>
                </div>
            </form>
        </div>

        <!-- Search & Filter Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-5 mb-8">
            <form action="{{ route('admin.expenses.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="grid gap-4 sm:grid-cols-3 flex-1">
                    <div>
                        <label for="month" class="block text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 mb-1.5">Month</label>
                        <select 
                            name="month" 
                            id="month" 
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-bold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                            <option value="all" {{ $filterMonth == 'all' ? 'selected' : '' }}>All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 mb-1.5">Year</label>
                        <select 
                            name="year" 
                            id="year" 
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-bold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                            <option value="all" {{ $filterYear == 'all' ? 'selected' : '' }}>All Years</option>
                            @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="search" class="block text-[10px] font-extrabold uppercase tracking-widest text-zinc-400 mb-1.5">Search Description</label>
                        <input 
                            type="text" 
                            name="search" 
                            id="search" 
                            value="{{ $search }}" 
                            placeholder="E.g. Electricity, salary..."
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                        >
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        type="submit" 
                        class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold py-2.5 px-5 rounded-lg text-xs shadow transition flex items-center gap-1.5 w-full md:w-auto justify-center"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Apply Filters
                    </button>
                    <a 
                        href="{{ route('admin.expenses.index') }}" 
                        class="bg-white hover:bg-zinc-50 text-zinc-700 font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm border border-zinc-250 transition block text-center"
                    >
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-200 bg-zinc-50 flex items-center justify-between">
                <h3 class="font-extrabold text-zinc-950 text-base">Expense Log Entries</h3>
                <span class="text-xs font-bold bg-zinc-100 text-zinc-700 px-2.5 py-1 rounded-full">{{ $expenses->count() }} Entries Logged</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-zinc-400 uppercase tracking-wider">Receipt</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($expenses as $exp)
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
                                            class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:text-teal-700 transition cursor-pointer"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Receipt
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
                                <td colspan="5" class="px-6 py-16 text-center text-zinc-400 font-semibold">No expense records logged for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
