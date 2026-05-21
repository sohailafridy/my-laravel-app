<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-zinc-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Page Header Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-blue-600 tracking-tight">{{ $title }}</h1>
                </div>

                <!-- Controls Suite -->
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Integrated Search Bar -->
                    <form method="GET" action="{{ route('admin.customers.list', $type) }}" class="flex items-center">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search customers..."
                            class="h-10 w-64 rounded-l-md border border-zinc-300 bg-white px-4 text-sm font-medium text-zinc-950 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:ring-1"
                        >
                        <button
                            type="submit"
                            class="inline-flex h-10 w-12 items-center justify-center rounded-r-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700 focus:outline-none"
                            aria-label="Search"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    <!-- Add Customer Button -->
                    <a
                        href="{{ route('admin.orders.create') }}"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-blue-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Add Customer
                    </a>
                </div>
            </div>

            <!-- Customers Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-0">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4 w-[10%]">ID</th>
                                <th class="px-6 py-4 w-[25%]">Name</th>
                                <th class="px-6 py-4 w-[25%]">Email</th>
                                <th class="px-6 py-4 w-[15%]">Phone</th>
                                <th class="px-6 py-4 w-[13%]">Type</th>
                                <th class="px-6 py-4 w-[12%] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($customers as $customer)
                                <tr class="hover:bg-slate-50 transition">
                                    <!-- ID -->
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        {{ $customer->id }}
                                    </td>
                                    <!-- Name -->
                                    <td class="px-6 py-4 font-extrabold text-slate-900">
                                        <a href="{{ route('admin.customer.ledger', $customer->id) }}" class="hover:text-blue-600 transition">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <!-- Email -->
                                    <td class="px-6 py-4 font-semibold text-slate-600">
                                        {{ $customer->email ?? '-' }}
                                    </td>
                                    <!-- Phone -->
                                    <td class="px-6 py-4 font-semibold text-slate-600">
                                        {{ $customer->phone ?? '-' }}
                                    </td>
                                    <!-- Type (Dynamic Badge styling) -->
                                    <td class="px-6 py-4">
                                        @if($customer->customer_type === 'outside')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide">
                                                Outside
                                            </span>
                                        @elseif($customer->customer_type === 'distributor')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200 uppercase tracking-wide">
                                                Distributor
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wide">
                                                Retail
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-center">
                                        <a
                                            href="{{ route('admin.customer.ledger', $customer->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 border border-slate-200 rounded-md text-xs font-bold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900 whitespace-nowrap"
                                        >
                                            Ledger
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                        No customers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
