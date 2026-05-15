        @php
            $menuItems = $menuItems ?? [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => true, 'icon' => 'dashboard'],
                ['label' => 'Users', 'href' => '#', 'active' => false, 'icon' => 'users'],
                ['label' => 'Orders', 'href' => '#', 'active' => false, 'icon' => 'orders'],
                ['label' => 'Analytics', 'href' => '#', 'active' => false, 'icon' => 'analytics'],
                ['label' => 'Messages', 'href' => '#', 'active' => false, 'icon' => 'messages'],
                ['label' => 'Settings', 'href' => '#', 'active' => false, 'icon' => 'settings'],
            ];

            $inventoryItems = [
                ['label' => 'Add Stock', 'href' => route('admin.products')],
                ['label' => 'Restock', 'href' => '#'],
                ['label' => 'Product Summary', 'href' => '#'],
                ['label' => 'Product Movement', 'href' => '#'],
                ['label' => 'Purchases', 'href' => '#'],
            ];
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
                        class="grid h-9 w-9 place-items-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 lg:hidden"
                        @click="sidebarOpen = false"
                        aria-label="Close sidebar"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
                    @foreach ($menuItems as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="{{ $item['active'] ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }} group flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition"
                        >
                            <span class="{{ $item['active'] ? 'text-teal-300' : 'text-zinc-400 group-hover:text-zinc-700' }} grid h-8 w-8 place-items-center rounded-lg transition">
                                @switch($item['icon'])
                                    @case('dashboard')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h7V4H4v9Zm0 7h7v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" />
                                        </svg>
                                        @break
                                    @case('users')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 20a8 8 0 0 1 16 0" />
                                        </svg>
                                        @break
                                    @case('orders')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12l2 5H4l2-5Zm-2 5v13h16V8M9 13h6" />
                                        </svg>
                                        @break
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
                    @endforeach

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
                                    class="block rounded-lg px-3 py-2 text-sm font-semibold text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950"
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
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
