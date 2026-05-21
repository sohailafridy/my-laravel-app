@php
            $menuItems = $menuItems ?? [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'dashboard'],
                // ['label' => 'Analytics', 'href' => '#', 'active' => false, 'icon' => 'analytics'],
                // ['label' => 'Messages', 'href' => '#', 'active' => false, 'icon' => 'messages'],
                // ['label' => 'Settings', 'href' => '#', 'active' => false, 'icon' => 'settings'],
            ];

            $inventoryItems = [
                ['label' => 'Stock', 'href' => route('admin.products'), 'active' => request()->routeIs('admin.products*')],
                ['label' => 'Restock', 'href' => route('admin.restock'), 'active' => request()->routeIs('admin.restock*')],
                ['label' => 'Product Summary', 'href' => route('admin.product.summary'), 'active' => request()->routeIs('admin.product.summary*')],
                ['label' => 'Product Movement', 'href' => route('admin.product.movement'), 'active' => request()->routeIs('admin.product.movement*')],
                ['label' => 'Purchases', 'href' => route('admin.purchases'), 'active' => request()->routeIs('admin.purchases*')],
            ];

            $orderItems = [];
            foreach ([
                ['label' => 'Make Order', 'route' => 'admin.orders.create'],
                ['label' => 'Order List', 'route' => 'admin.orders.index'],
            ] as $item) {
                $orderItems[] = [
                    'label' => $item['label'],
                    'href' => \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#',
                    'active' => request()->routeIs($item['route']),
                ];
            }

            $currentType = null;
            if (request()->routeIs('admin.customers.list')) {
                $currentType = request()->route('type');
            } elseif (request()->routeIs('admin.customer.ledger')) {
                $customerParam = request()->route('customer');
                if (is_object($customerParam)) {
                    $currentType = $customerParam->customer_type;
                } elseif (is_numeric($customerParam)) {
                    $currentType = \App\Models\Customer::where('id', $customerParam)->value('customer_type');
                }
            }
        @endphp
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-zinc-950/45 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                x-cloak
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-50 flex w-[280px] transform flex-col border-r border-zinc-200 bg-white transition-transform duration-300 ease-out lg:sticky lg:top-0 lg:h-screen"
            >
                <div class="flex h-16 items-center justify-between border-b border-zinc-200 px-5">
                    <a href="#" class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-zinc-950 text-sm font-bold text-white motion-glow">AD</span>
                        <span>
                            <span class="block text-sm font-bold leading-5">Admin Panel</span>
                            <span class="block text-xs font-medium text-zinc-500">Control Center</span>
                        </span>
                    </a>

                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 lg:hidden"
                        @click="sidebarOpen = false"
                        aria-label="Close sidebar"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
                    <!-- Dashboard Link -->
                    <a
                        href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }} group flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition"
                    >
                        <span class="{{ request()->routeIs('dashboard') ? 'text-teal-300' : 'text-zinc-400 group-hover:text-zinc-700' }} grid h-8 w-8 place-items-center rounded-lg transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h7V4H4v9Zm0 7h7v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" />
                            </svg>
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <!-- Collapsible Users Link (Bold) -->
                    <div class="pt-1">
                        <button
                            type="button"
                            class="group flex h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-extrabold text-zinc-950 transition hover:bg-zinc-100 hover:text-zinc-950"
                            @click="usersOpen = ! usersOpen"
                            :aria-expanded="usersOpen.toString()"
                        >
                            <span class="grid h-8 w-8 place-items-center rounded-lg text-zinc-950 transition group-hover:text-zinc-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 20a8 8 0 0 1 16 0" />
                                </svg>
                            </span>
                            <span class="flex-1 text-zinc-950">Users</span>
                            <svg
                                class="h-4 w-4 text-zinc-400 transition duration-200"
                                :class="{ 'rotate-180 text-zinc-700': usersOpen }"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="usersOpen"
                            x-transition
                            class="mt-1 space-y-1 border-l border-zinc-200 pl-6"
                        >
                            <!-- Submenu 1: Customer -->
                            <a
                                href="{{ route('admin.customers.list', 'customer') }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ ($currentType === 'customer' || request()->is('admin/customers/list/customer')) ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Customer
                            </a>
                            <!-- Submenu 2: Outside Customer -->
                            <a
                                href="{{ route('admin.customers.list', 'outside') }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ ($currentType === 'outside' || request()->is('admin/customers/list/outside')) ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Outside Customer
                            </a>
                            <!-- Submenu 3: Destributer -->
                            <a
                                href="{{ route('admin.customers.list', 'distributor') }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ ($currentType === 'distributor' || request()->is('admin/customers/list/distributor')) ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Destributer
                            </a>
                            <!-- Submenu 4: Outstanding Dues -->
                            <a
                                href="{{ Route::has('admin.customers.dues') ? route('admin.customers.dues') : '#' }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.customers.dues') ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Outstanding Dues
                            </a>
                        </div>
                    </div>

                    <!-- Other static menu items -->
                    @foreach ($menuItems as $item)
                        @if($item['label'] !== 'Dashboard' && $item['label'] !== 'Users')
                            <a
                                href="{{ $item['href'] }}"
                                class="{{ $item['active'] ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }} group flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition"
                            >
                                <span class="{{ $item['active'] ? 'text-teal-300' : 'text-zinc-400 group-hover:text-zinc-700' }} grid h-8 w-8 place-items-center rounded-lg transition">
                                    @switch($item['icon'])
                                        @case('analytics')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m4 14v-8m4 8V7m4 12v-5m4 5H4" />
                                            </svg>
                                            @break
                                        @case('messages')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" />
                                            </svg>
                                            @break
                                        @default
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.09 1.65V21a2 2 0 1 1-4 0v-.08a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.97.36l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.09H3a2 2 0 1 1 0-4h.08A1.8 1.8 0 0 0 4.73 8.8a1.8 1.8 0 0 0-.36-1.97l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05a1.8 1.8 0 0 0 1.98.36h.01A1.8 1.8 0 0 0 10.28 2.7V2a2 2 0 1 1 4 0v.08a1.8 1.8 0 0 0 1.09 1.65 1.8 1.8 0 0 0 1.98-.36l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05a1.8 1.8 0 0 0-.36 1.98v.01a1.8 1.8 0 0 0 1.65 1.09H21a2 2 0 1 1 0 4h-.08A1.8 1.8 0 0 0 19.4 15Z" />
                                            </svg>
                                    @endswitch
                                </span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach

                    <div class="pt-1">
                        <button
                            type="button"
                            class="group flex h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950"
                            @click="orderOpen = ! orderOpen"
                            :aria-expanded="orderOpen.toString()"
                        >
                            <span class="grid h-8 w-8 place-items-center rounded-lg text-zinc-400 transition group-hover:text-zinc-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12l2 5H4l2-5Zm-2 5v13h16V8M9 13h6" />
                                </svg>
                            </span>
                            <span class="flex-1">Order</span>
                            <svg
                                class="h-4 w-4 text-zinc-400 transition duration-200"
                                :class="{ 'rotate-180 text-zinc-700': orderOpen }"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="orderOpen"
                            x-transition
                            class="mt-1 space-y-1 border-l border-zinc-200 pl-6"
                        >
                            @foreach ($orderItems as $item)
                                <a
                                    href="{{ $item['href'] }}"
                                    class="block rounded-lg px-3 py-2 text-sm font-semibold {{ $item['active'] ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-1">
                        <button
                            type="button"
                            class="group flex h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950"
                            @click="inventoryOpen = ! inventoryOpen"
                            :aria-expanded="inventoryOpen.toString()"
                        >
                            <span class="grid h-8 w-8 place-items-center rounded-lg text-zinc-400 transition group-hover:text-zinc-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.3 7 8.7 5 8.7-5M12 22V12" />
                                </svg>
                            </span>
                            <span class="flex-1">Inventory</span>
                            <svg
                                class="h-4 w-4 text-zinc-400 transition duration-200"
                                :class="{ 'rotate-180 text-zinc-700': inventoryOpen }"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="inventoryOpen"
                            x-transition
                            class="mt-1 space-y-1 border-l border-zinc-200 pl-6"
                        >
                            @foreach ($inventoryItems as $item)
                                <a
                                    href="{{ $item['href'] }}"
                                    class="block rounded-lg px-3 py-2 text-sm font-semibold {{ $item['active'] ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Expenses Link Group -->
                    <div class="pt-1">
                        <button
                            type="button"
                            class="group flex h-11 w-full items-center gap-3 rounded-lg px-3 text-left text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950"
                            @click="expensesOpen = ! expensesOpen"
                            :aria-expanded="expensesOpen.toString()"
                        >
                            <span class="grid h-8 w-8 place-items-center rounded-lg text-zinc-400 transition group-hover:text-zinc-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879-.659c1.171-.879 3.07-.879 4.242 0 1.172.879 1.172 2.303 0 3.182C13.536 21.371 12.36 21 12 21M9.01 9h5.98" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" />
                                </svg>
                            </span>
                            <span class="flex-1">Expenses</span>
                            <svg
                                class="h-4 w-4 text-zinc-400 transition duration-200"
                                :class="{ 'rotate-180 text-zinc-700': expensesOpen }"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="expensesOpen"
                            x-transition
                            class="mt-1 space-y-1 border-l border-zinc-200 pl-6"
                        >
                            <a
                                href="{{ Route::has('admin.expenses.index') ? route('admin.expenses.index') : '#' }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.expenses.index') ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Log Expense
                            </a>
                            <a
                                href="{{ Route::has('admin.expenses.categories') ? route('admin.expenses.categories') : '#' }}"
                                class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.expenses.categories') ? 'bg-zinc-100 text-zinc-950 shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-950' }} transition"
                            >
                                Categories
                            </a>
                        </div>
                    </div>
                </nav>

                <div class="border-t border-zinc-200 p-4">
                    <div class="rounded-lg border border-teal-200 bg-teal-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-teal-950">Storage</p>
                                <p class="text-xs font-medium text-teal-700">72% used</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white text-teal-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7c0 2 3.58 4 8 4s8-2 8-4-3.58-4-8-4-8 2-8 4Zm0 0v10c0 2 3.58 4 8 4s8-2 8-4V7" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12c0 2 3.58 4 8 4s8-2 8-4" />
                                </svg>
                            </span>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-teal-100">
                            <div class="motion-bar h-full rounded-full bg-teal-500" style="width: 72%;"></div>
                        </div>
                    </div>
                </div>
            </aside>
