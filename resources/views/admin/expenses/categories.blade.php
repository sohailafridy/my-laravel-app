<x-app-layout>
    <x-slot name="title">Operational Expense Categories</x-slot>

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

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Validation failed:</span>
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
                <h1 class="text-3xl font-extrabold text-zinc-950">Expense Categories</h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">Configure expense types and review category cost allocation summaries</p>
            </div>
            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('admin.expenses.index') }}"
                    class="bg-white hover:bg-zinc-50 text-zinc-700 font-bold py-2.5 px-4 rounded-lg flex items-center shadow border border-zinc-200 transition text-sm gap-2"
                >
                    <svg class="h-5 w-5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Log Ledger
                </a>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[340px_minmax(0,1fr)]">
            <!-- Add Category Card -->
            <aside class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
                    <h3 class="font-extrabold text-zinc-950 text-base mb-4">Add Category</h3>

                    <form action="{{ route('admin.expenses.categories.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="expense_type" class="block text-xs font-bold uppercase tracking-wider text-zinc-500 mb-2">Category Name *</label>
                            <input 
                                type="text" 
                                name="expense_type" 
                                id="expense_type" 
                                required 
                                maxlength="20"
                                placeholder="E.g. Electricity, Rent"
                                class="w-full rounded-lg border border-zinc-250 bg-white px-3.5 py-2.5 text-sm font-bold text-zinc-950 focus:border-zinc-950 focus:outline-none focus:ring-1 focus:ring-zinc-950"
                            >
                            <span class="text-[10px] text-zinc-400 font-medium mt-1 block">Maximum length: 20 characters</span>
                        </div>

                        <button 
                            type="submit" 
                            class="bg-zinc-950 hover:bg-zinc-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition text-sm flex items-center justify-center gap-1.5 w-full"
                        >
                            <svg class="h-4 w-4 text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Save Category
                        </button>
                    </form>
                </div>

                <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 text-white shadow-md">
                    <h4 class="font-extrabold text-white text-sm mb-2 flex items-center gap-2">
                        <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 113.536 0V21h2v-2.162" />
                        </svg>
                        Operational Tip:
                    </h4>
                    <p class="text-xs text-zinc-400 font-semibold leading-relaxed">
                        Categories enable quick segmentation. Maintain distinct category tags (e.g. Utility Bills, Store Rent, Staff Salaries, Freight/Delivery) for clean tax audits and net-profit calculations.
                    </p>
                </div>
            </aside>

            <!-- Categories Grid List -->
            <section class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-5">
                    <h3 class="font-extrabold text-zinc-950 text-base mb-1">Active Expense Categories</h3>
                    <p class="text-xs font-semibold text-zinc-450">Review cost-volume totals per category type</p>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @forelse ($categories as $cat)
                        <article class="motion-card bg-white rounded-xl border border-zinc-200 shadow-sm p-6 flex flex-col justify-between transition hover:-translate-y-1 hover:shadow-md">
                            <div>
                                <div class="flex items-start justify-between">
                                    <h4 class="font-extrabold text-zinc-950 text-lg">{{ $cat->expense_type }}</h4>
                                    <span class="inline-flex items-center rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700 border border-zinc-200">
                                        {{ $cat->expenses_count }} Logged
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <span class="text-xs font-bold text-zinc-400 block uppercase tracking-wider">Total Outflow</span>
                                    <span class="text-2xl font-black text-rose-600">
                                        PKR {{ number_format($cat->total_spent ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-400 font-semibold">
                                <span>Created At:</span>
                                <span>{{ \Carbon\Carbon::parse($cat->created_at)->format('M d, Y') }}</span>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-2 bg-white rounded-xl border border-zinc-200 shadow-sm p-12 text-center text-zinc-400 font-semibold">
                            No expense categories defined yet. Create your first category on the left panel!
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
