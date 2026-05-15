<header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/90 backdrop-blur">
                    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="grid h-10 w-10 place-items-center rounded-lg border border-zinc-200 text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950 lg:hidden"
                                @click="sidebarOpen = true"
                                aria-label="Open sidebar"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-teal-700">Admin Overview</p>
                                <h1 class="text-xl font-bold text-zinc-950 sm:text-2xl">
                                    @if(Auth::check() && auth()->user()->user_type === '1')
                                        Admin Dashboard
                                    @elseif(Auth::check() && auth()->user()->user_type === '2')
                                        Customer Dashboard
                                        @elseif(Auth::check() && auth()->user()->user_type === '3')
                                        Staff Dashboard
                                    @endif
                                </h1>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="hidden h-10 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-600 shadow-sm sm:flex">
                                <svg class="h-4 w-4 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                </svg>
                                Search
                            </div>

                            <button type="button" class="relative grid h-10 w-10 place-items-center rounded-lg border border-zinc-200 bg-white text-zinc-600 shadow-sm transition hover:bg-zinc-100 hover:text-zinc-950" aria-label="Notifications">
                                <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM13.73 21a2 2 0 0 1-3.46 0" />
                                </svg>
                            </button>

                            <div x-data="{ accountOpen: false }" class="relative">
                                <button
                                    type="button"
                                    class="flex h-10 items-center gap-3 rounded-lg border border-zinc-200 bg-white px-2 shadow-sm transition hover:bg-zinc-100"
                                    aria-label="Account menu"
                                    @click="accountOpen = ! accountOpen"
                                    :aria-expanded="accountOpen.toString()"
                                >
                                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-amber-100 text-xs font-bold text-amber-800">AK</span>
                                    <span class="hidden pr-1 text-sm font-semibold text-zinc-700 sm:inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                                    <svg
                                        class="h-4 w-4 text-zinc-500 transition"
                                        :class="{ 'rotate-180': accountOpen }"
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
                                    x-show="accountOpen"
                                    x-transition.origin.top.right
                                    @click.outside="accountOpen = false"
                                    class="absolute right-0 z-40 mt-2 w-56 overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl"
                                >
                                    <div class="border-b border-zinc-100 px-4 py-3">
                                        <p class="text-sm font-bold text-zinc-950">{{ auth()->user()->name ?? 'Admin' }}</p>
                                        <p class="truncate text-xs font-medium text-zinc-500">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                                    </div>

                                    <a
                                        href="{{ route('profile.edit') }}"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 hover:text-zinc-950"
                                    >
                                        <svg class="h-4 w-4 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21a8 8 0 0 1 16 0" />
                                        </svg>
                                        Profile
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}" class="logout-form border-t border-zinc-100">
                                        @csrf

                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-semibold text-rose-700 transition hover:bg-rose-50"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5M15 12H3" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 19V5a2 2 0 0 0-2-2h-7" />
                                            </svg>
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>