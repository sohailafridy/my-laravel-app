<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    @php
        $stats = [
            ['label' => 'Total Revenue', 'value' => '$48.2K', 'change' => '+18.4%', 'tone' => 'text-emerald-700 bg-emerald-50 border-emerald-200', 'bar' => 'bg-emerald-500', 'width' => '82%'],
            ['label' => 'Active Users', 'value' => '12,840', 'change' => '+9.7%', 'tone' => 'text-sky-700 bg-sky-50 border-sky-200', 'bar' => 'bg-sky-500', 'width' => '70%'],
            ['label' => 'Pending Orders', 'value' => '326', 'change' => '-3.1%', 'tone' => 'text-amber-700 bg-amber-50 border-amber-200', 'bar' => 'bg-amber-500', 'width' => '54%'],
            ['label' => 'Conversion', 'value' => '8.42%', 'change' => '+2.9%', 'tone' => 'text-rose-700 bg-rose-50 border-rose-200', 'bar' => 'bg-rose-500', 'width' => '62%'],
        ];

        $activities = [
            ['name' => 'Ayesha Khan', 'event' => 'New subscription activated', 'status' => 'Completed', 'time' => '2 min ago'],
            ['name' => 'Bilal Ahmed', 'event' => 'Order #8742 needs review', 'status' => 'Pending', 'time' => '12 min ago'],
            ['name' => 'Sara Malik', 'event' => 'Profile verification submitted', 'status' => 'In Review', 'time' => '28 min ago'],
            ['name' => 'Hamza Ali', 'event' => 'Invoice payment received', 'status' => 'Completed', 'time' => '1 hour ago'],
        ];
    @endphp

    <section class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-5">
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($stats as $stat)
                        <article class="motion-card rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md" style="animation-delay: {{ $loop->index * 90 }}ms;">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-500">{{ $stat['label'] }}</p>
                                    <p class="mt-2 text-2xl font-bold text-zinc-950">{{ $stat['value'] }}</p>
                                </div>
                                <span class="{{ $stat['tone'] }} rounded-lg border px-2.5 py-1 text-xs font-bold">{{ $stat['change'] }}</span>
                            </div>
                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-zinc-100">
                                <div class="{{ $stat['bar'] }} motion-bar h-full rounded-full" style="width: {{ $stat['width'] }}; animation-delay: {{ 220 + ($loop->index * 120) }}ms;"></div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="motion-card rounded-lg border border-zinc-200 bg-white shadow-sm" style="animation-delay: 180ms;">
                    <div class="flex flex-col gap-3 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Revenue Flow</h2>
                            <p class="text-sm font-medium text-zinc-500">Monthly performance snapshot</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" class="rounded-lg bg-zinc-950 px-3 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800">Month</button>
                            <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">Year</button>
                        </div>
                    </div>

                    <div class="grid min-h-[320px] gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_220px]">
                        <div class="flex min-h-[260px] items-end gap-3 rounded-lg border border-zinc-100 bg-zinc-50 p-4">
                            @foreach ([48, 62, 44, 78, 58, 86, 72, 94, 68, 82, 76, 90] as $height)
                                <div class="flex h-full flex-1 items-end">
                                    <div
                                        class="motion-bar w-full rounded-t-lg bg-zinc-900 transition duration-300 hover:bg-teal-500"
                                        style="height: {{ $height }}%; animation-delay: {{ $loop->index * 70 }}ms;"
                                    ></div>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid gap-3">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                                <p class="text-sm font-semibold text-emerald-700">Net Profit</p>
                                <p class="mt-2 text-2xl font-bold text-emerald-950">$18.6K</p>
                            </div>
                            <div class="rounded-lg border border-sky-200 bg-sky-50 p-4">
                                <p class="text-sm font-semibold text-sky-700">Avg. Order</p>
                                <p class="mt-2 text-2xl font-bold text-sky-950">$142</p>
                            </div>
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-semibold text-amber-700">Refund Rate</p>
                                <p class="mt-2 text-2xl font-bold text-amber-950">1.8%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="motion-card rounded-lg border border-zinc-200 bg-white shadow-sm" style="animation-delay: 260ms;">
                    <div class="flex items-center justify-between border-b border-zinc-200 p-5">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Recent Activity</h2>
                            <p class="text-sm font-medium text-zinc-500">Latest admin events</p>
                        </div>
                        <button type="button" class="grid h-9 w-9 place-items-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950" aria-label="More activity">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01" />
                            </svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-left text-sm">
                            <thead class="bg-zinc-50 text-xs font-bold uppercase text-zinc-500">
                                <tr>
                                    <th class="px-5 py-3">User</th>
                                    <th class="px-5 py-3">Event</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                @foreach ($activities as $activity)
                                    <tr class="transition hover:bg-zinc-50">
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-zinc-950">{{ $activity['name'] }}</td>
                                        <td class="min-w-[220px] px-5 py-4 text-zinc-600">{{ $activity['event'] }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-bold text-zinc-700">{{ $activity['status'] }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-zinc-500">{{ $activity['time'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <section class="motion-card rounded-lg border border-zinc-200 bg-white p-5 shadow-sm" style="animation-delay: 220ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Team Pulse</h2>
                            <p class="text-sm font-medium text-zinc-500">Live workload</p>
                        </div>
                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Online</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach ([['Design', '76%', 'bg-teal-500'], ['Support', '58%', 'bg-sky-500'], ['Sales', '84%', 'bg-rose-500']] as $pulse)
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span class="font-semibold text-zinc-700">{{ $pulse[0] }}</span>
                                    <span class="font-bold text-zinc-950">{{ $pulse[1] }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-zinc-100">
                                    <div class="{{ $pulse[2] }} motion-bar h-full rounded-full" style="width: {{ $pulse[1] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="motion-card rounded-lg border border-zinc-200 bg-zinc-950 p-5 text-white shadow-sm" style="animation-delay: 300ms;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold">Quick Actions</h2>
                        <span class="rounded-lg bg-white/10 px-2.5 py-1 text-xs font-bold text-teal-200">Admin</span>
                    </div>

                    <div class="mt-5 grid gap-3">
                        <button type="button" class="flex h-11 items-center justify-between rounded-lg bg-white px-3 text-sm font-bold text-zinc-950 transition hover:bg-teal-100">
                            Add New User
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button type="button" class="flex h-11 items-center justify-between rounded-lg bg-white/10 px-3 text-sm font-bold text-white transition hover:bg-white/15">
                            Export Report
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                            </svg>
                        </button>
                    </div>
                </section>

                <section class="motion-card rounded-lg border border-zinc-200 bg-white p-5 shadow-sm" style="animation-delay: 380ms;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-zinc-950">Tasks</h2>
                            <p class="text-sm font-medium text-zinc-500">Today</p>
                        </div>
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-amber-100 text-amber-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @foreach (['Review new products', 'Approve vendor payout', 'Check support queue'] as $task)
                            <label class="flex items-center gap-3 rounded-lg border border-zinc-200 px-3 py-3 transition hover:bg-zinc-50">
                                <input type="checkbox" class="rounded border-zinc-300 text-zinc-950 focus:ring-zinc-950">
                                <span class="text-sm font-semibold text-zinc-700">{{ $task }}</span>
                            </label>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-app-layout>
