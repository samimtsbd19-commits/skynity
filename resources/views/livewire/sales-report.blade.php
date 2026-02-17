<div>
    @section('title', 'বিক্রয় রিপোর্ট')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white">বিক্রয় রিপোর্ট</h1>
            <p class="text-gray-500 text-sm mt-0.5">ভাউচার বিক্রয়ের সম্পূর্ণ বিবরণ</p>
        </div>
        <button wire:click="export"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-emerald-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            এক্সপোর্ট CSV
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-gradient-to-br from-emerald-900/40 to-emerald-800/20 rounded-2xl p-5 border border-emerald-700/40 card-hover">
            <p class="text-emerald-400 text-xs font-bold uppercase tracking-wider">আজকের বিক্রয়</p>
            <p class="text-3xl font-black text-white mt-2">৳{{ number_format($todaySales) }}</p>
            <p class="text-emerald-700 text-xs mt-1">আজ</p>
        </div>
        <div class="bg-gradient-to-br from-blue-900/40 to-blue-800/20 rounded-2xl p-5 border border-blue-700/40 card-hover">
            <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">এই সপ্তাহে</p>
            <p class="text-3xl font-black text-white mt-2">৳{{ number_format($weekSales) }}</p>
            <p class="text-blue-700 text-xs mt-1">৭ দিন</p>
        </div>
        <div class="bg-gradient-to-br from-purple-900/40 to-purple-800/20 rounded-2xl p-5 border border-purple-700/40 card-hover">
            <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">এই মাসে</p>
            <p class="text-3xl font-black text-white mt-2">৳{{ number_format($monthSales) }}</p>
            <p class="text-purple-700 text-xs mt-1">{{ now()->format('F') }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-900/40 to-indigo-800/20 rounded-2xl p-5 border border-indigo-700/40 card-hover">
            <p class="text-indigo-400 text-xs font-bold uppercase tracking-wider">মোট বিক্রয়</p>
            <p class="text-3xl font-black text-white mt-2">৳{{ number_format($totalSales) }}</p>
            <p class="text-indigo-700 text-xs mt-1">সর্বমোট</p>
        </div>
    </div>

    <!-- Chart -->
    @if(isset($chartData) && count($chartData) > 0)
    <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            বিক্রয় চার্ট (শেষ ৩০ দিন)
        </h3>
        <div class="h-48">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">শুরুর তারিখ</label>
                <input type="date" wire:model.live="startDate"
                       class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-200 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">শেষের তারিখ</label>
                <input type="date" wire:model.live="endDate"
                       class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-200 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">রাউটার</label>
                <select wire:model.live="filterRouter"
                        class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-indigo-500 transition">
                    <option value="">সব রাউটার</option>
                    @foreach($routers as $router)
                        <option value="{{ $router->id }}">{{ $router->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">পেমেন্ট মেথড</label>
                <select wire:model.live="filterPayment"
                        class="w-full px-4 py-2.5 bg-gray-900/60 border border-gray-700 rounded-xl text-gray-300 text-sm focus:outline-none focus:border-indigo-500 transition">
                    <option value="">সব মেথড</option>
                    <option value="cash">নগদ</option>
                    <option value="bkash">বিকাশ</option>
                    <option value="nagad">নগদ মোবাইল</option>
                    <option value="other">অন্যান্য</option>
                </select>
            </div>
        </div>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden sm:block bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900/50">
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-4">#</th>
                        <th class="px-5 py-4">তারিখ</th>
                        <th class="px-5 py-4">ইউজারনেম</th>
                        <th class="px-5 py-4">প্যাকেজ</th>
                        <th class="px-5 py-4">রাউটার</th>
                        <th class="px-5 py-4">পেমেন্ট</th>
                        <th class="px-5 py-4 text-right">মূল্য</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/30">
                    @forelse($sales as $index => $sale)
                    <tr class="hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-3.5 text-gray-600 text-sm">{{ $sales->firstItem() + $index }}</td>
                        <td class="px-5 py-3.5 text-gray-400 text-sm">{{ $sale->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-bold text-cyan-400 text-sm">{{ $sale->voucher->username ?? 'N/A' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 text-sm">{{ $sale->package->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-sm">{{ $sale->router->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $pm = $sale->payment_method ?? 'other';
                                $pmLabels = ['cash' => ['নগদ','emerald'], 'bkash' => ['বিকাশ','pink'], 'nagad' => ['নগদ মোবাইল','orange'], 'other' => ['অন্যান্য','gray']];
                                [$pmLabel, $pmColor] = $pmLabels[$pm] ?? ['অন্যান্য', 'gray'];
                            @endphp
                            <span class="px-2.5 py-1 bg-{{ $pmColor }}-500/15 text-{{ $pmColor }}-400 text-xs font-bold rounded-full border border-{{ $pmColor }}-500/30">
                                {{ $pmLabel }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-emerald-400">৳{{ number_format($sale->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-700/50 flex items-center justify-center mb-4 mx-auto">
                                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">কোনো বিক্রয় রেকর্ড নেই</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                <tfoot class="bg-gray-900/40 border-t border-gray-700/40">
                    <tr>
                        <td colspan="6" class="px-5 py-4 text-right text-gray-400 font-semibold text-sm">পেজের মোট:</td>
                        <td class="px-5 py-4 text-right font-black text-emerald-400 text-lg">৳{{ number_format($sales->sum('amount'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($sales->hasPages())
        <div class="px-5 py-4 border-t border-gray-700/40">{{ $sales->links() }}</div>
        @endif
    </div>

    <!-- MOBILE CARDS -->
    <div class="sm:hidden space-y-3">
        @forelse($sales as $sale)
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl border border-gray-700/60 p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="font-mono font-bold text-cyan-400">{{ $sale->voucher->username ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $sale->created_at->format('d M Y H:i') }}</p>
                </div>
                <p class="font-black text-emerald-400 text-lg">৳{{ number_format($sale->amount) }}</p>
            </div>
            <div class="flex gap-2 flex-wrap text-xs mt-2">
                <span class="px-2 py-1 bg-gray-700 text-gray-400 rounded-lg">{{ $sale->package->name ?? '—' }}</span>
                <span class="px-2 py-1 bg-gray-700 text-gray-400 rounded-lg">{{ $sale->router->name ?? '—' }}</span>
                @php $pm = $sale->payment_method ?? 'other'; @endphp
                <span class="px-2 py-1 bg-indigo-500/15 text-indigo-400 rounded-lg border border-indigo-500/20">{{ $pm }}</span>
            </div>
        </div>
        @empty
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/60 p-12 text-center">
            <p class="text-gray-500">কোনো রেকর্ড নেই</p>
        </div>
        @endforelse

        @if($sales->hasPages())
        <div class="py-2">{{ $sales->links() }}</div>
        @endif
    </div>

    @if(isset($chartData) && count($chartData) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            const data = @json($chartData);
            const labels = data.map(d => d.date);
            const amounts = data.map(d => d.amount);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'বিক্রয় (৳)',
                        data: amounts,
                        borderColor: '#818cf8',
                        backgroundColor: 'rgba(129,140,248,0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#818cf8',
                        pointRadius: 3,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            borderColor: '#374151',
                            borderWidth: 1,
                            titleColor: '#9ca3af',
                            bodyColor: '#f3f4f6',
                            callbacks: {
                                label: (ctx) => '৳' + ctx.raw.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: '#6b7280', font: { size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: {
                                color: '#6b7280', font: { size: 11 },
                                callback: (v) => '৳' + v.toLocaleString()
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</div>
