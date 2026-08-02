@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300">

    <!-- Hero Action & Motivation Card (Mindful Spending Focus) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 dark:from-slate-900 dark:via-indigo-950 dark:to-slate-900 text-white p-6 sm:p-7 shadow-xl border border-indigo-500/20 dark:border-indigo-500/30 mb-6 transition-all duration-300">
        <!-- Subtle Glow Effect -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Banner Top Header: Greeting (Left) & Real-time Date/Clock (Right) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-white/15 relative z-10">
            <h1 class="text-lg sm:text-xl font-extrabold text-white flex items-center gap-2">
                <span>👋</span> Halo, {{ Auth::user()->name }}!
            </h1>
            <div id="userLiveClock" class="text-[11px] sm:text-xs text-indigo-100/90 font-semibold bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 self-start sm:self-auto shadow-sm">
                <span>📅 -- | 🕒 --</span>
            </div>
        </div>

        <!-- Cashflow & Single-Line Financial Health Advisory Row -->
        <div class="mt-4 relative z-10 space-y-3">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-indigo-200/90">Arus Kas Bulan Ini</span>
                <div class="flex items-center gap-3 mt-0.5">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Rp {{ number_format($monthlyCashflow, 0, ',', '.') }}
                    </h2>
                </div>
                <div class="mt-1 text-xs text-white/90 flex items-center gap-1.5 font-semibold">
                    <span>💡</span> <span class="italic">"Kelola pengeluaran secara bijak, hindari pembelian impulsif."</span>
                </div>
            </div>

            <!-- Single-Line (Segaris) Progress Bar & Warning Indicator -->
            <div class="pt-2 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                <div class="flex items-center gap-2.5 flex-1 max-w-xl">
                    <div class="w-24 sm:w-32 bg-white/20 rounded-full h-2 overflow-hidden shrink-0">
                        <div class="h-2 rounded-full transition-all duration-500 
                            @if($expenseRatio <= 25) bg-emerald-400
                            @elseif($expenseRatio <= 50) bg-sky-400
                            @elseif($expenseRatio <= 75) bg-amber-400
                            @else bg-rose-500 @endif" 
                            style="width: {{ min(100, $expenseRatio) }}%">
                        </div>
                    </div>
                    <span class="truncate font-medium text-white/95 text-[11px] sm:text-xs">
                        @if($expenseRatio <= 25)
                            🟢 <strong>Sangat Baik!</strong> {{ Auth::user()->name }}, pengeluaran baru {{ $expenseRatio }}% dari pemasukan.
                        @elseif($expenseRatio <= 50)
                            🔵 <strong>Mulai Waspada!</strong> {{ Auth::user()->name }}, pengeluaran {{ $expenseRatio }}% dari pemasukan.
                        @elseif($expenseRatio <= 75)
                            ⚠️ <strong>Perlu Hati-hati!</strong> {{ Auth::user()->name }}, pengeluaran sudah {{ $expenseRatio }}% dari pemasukan.
                        @else
                            🚨 <strong>⚠️ Peringatan Kritis!</strong> Menggunakan {{ $expenseRatio }}% pemasukan. Sisa saldo belanja: Rp {{ number_format(max(0, $monthlyCashflow), 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Action Quick Shortcuts inside Card -->
        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-white/20 relative z-10">
            <button onclick="openModal('incomeModal')" class="flex items-center justify-center gap-2 bg-emerald-500/30 hover:bg-emerald-500/40 border border-emerald-300/50 text-white py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition backdrop-blur-sm">
                <span>➕</span> Catat Pemasukan
            </button>
            <button onclick="openModal('expenseModal')" class="flex items-center justify-center gap-2 bg-rose-500/30 hover:bg-rose-500/40 border border-rose-300/50 text-white py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition backdrop-blur-sm">
                <span>➖</span> Catat Pengeluaran
            </button>
        </div>
    </div>

    <!-- Summary Widgets Grid (Bulan Ini) -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Pemasukan Card -->
        <a href="{{ route('income.index') }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Pemasukan Bulan Ini</span>
                <div class="p-2 rounded-xl bg-emerald-100/70 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-extrabold text-emerald-700 dark:text-emerald-400" id="incomeAmount">Rp {{ number_format($currentMonthIncome, 0, ',', '.') }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="text-[10px] sm:text-[11px] font-extrabold px-2 py-0.5 rounded-full {{ $incomeChangePct >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60' }}">
                    {{ $incomeChangePct >= 0 ? '↑ +' : '↓ ' }}{{ $incomeChangePct }}% vs bln lalu
                </span>
            </div>
        </a>

        <!-- Pengeluaran Card -->
        <a href="{{ route('expense.index') }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Pengeluaran Bulan Ini</span>
                <div class="p-2 rounded-xl bg-rose-100/70 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                </div>
            </div>
            <p class="text-lg sm:text-xl font-extrabold text-rose-700 dark:text-rose-400" id="expenseAmount">Rp {{ number_format($currentMonthExpense, 0, ',', '.') }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="text-[10px] sm:text-[11px] font-extrabold px-2 py-0.5 rounded-full {{ $expenseChangePct <= 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60' }}">
                    {{ $expenseChangePct > 0 ? '↑ +' : ($expenseChangePct < 0 ? '↓ ' : '') }}{{ $expenseChangePct }}% vs bln lalu
                </span>
            </div>
        </a>
    </div>

    <!-- Section Title: Charts -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <span>📊</span> Analisis & Grafik Keuangan
        </h3>
        <span class="text-xs text-slate-400">Real-time Analytics</span>
    </div>

    <!-- 4 Interactive Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        
        <!-- CHART 1: Grafik Tren Keuangan (Line/Area) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">📈 Tren Keuangan 30 Hari</h4>
                    <p class="text-[11px] text-slate-400">Pemasukan vs Pengeluaran harian (30 hari terakhir)</p>
                </div>
            </div>
            <div class="relative h-56 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- CHART 2: Pengeluaran per Kategori (Pie / Donut) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">🥧 Pengeluaran per Kategori</h4>
                    <p class="text-[11px] text-slate-400">Persentase alokasi dana</p>
                </div>
                <button onclick="toggleCategoryChartType()" id="catChartTypeBtn" class="text-[11px] bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 font-semibold px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition">
                    Tipe: Pie (Full)
                </button>
            </div>
            <div class="relative h-56 w-full flex items-center justify-center">
                @if(empty($chartData['categories']['data']) || array_sum($chartData['categories']['data']) == 0)
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-400">Belum ada data pengeluaran berkategori</p>
                    </div>
                @else
                    <canvas id="categoryChart"></canvas>
                @endif
            </div>
        </div>

        <!-- CHART 3: Arus Kas Bulanan (Bar) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">📊 Arus Kas Bulanan</h4>
                    <p class="text-[11px] text-slate-400">Perbandingan 6 bulan terakhir</p>
                </div>
            </div>
            <div class="relative h-56 w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- CHART 4: Target Keuangan / Financial Goals (Progress Bars) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">🎯 Progress Target Keuangan</h4>
                    <p class="text-[11px] text-slate-400">Pencapaian Financial Goals</p>
                </div>
                <a href="{{ route('goals.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua →</a>
            </div>
            <div class="relative h-56 w-full overflow-y-auto pr-1">
                @if($goals->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-center py-6">
                        <span class="text-2xl mb-1">🎯</span>
                        <p class="text-xs text-slate-400">Belum ada target keuangan yang dibuat.</p>
                        <a href="{{ route('goals.create') }}" class="mt-3 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-semibold transition">
                            + Buat Target Pertama
                        </a>
                    </div>
                @else
                    <div class="space-y-3 pt-1">
                        @foreach($goals as $goal)
                            @php
                                $pct = $goal->target > 0 ? min(100, round(($goal->progress / $goal->target) * 100)) : 0;
                            @endphp
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between items-center text-xs mb-1.5">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[150px]">{{ $goal->title }}</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                                    <span>Rp {{ number_format($goal->progress, 0, ',', '.') }}</span>
                                    <span>Target: Rp {{ number_format($goal->target, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Section Title: Recent Transactions -->
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <span>📝</span> Transaksi Terakhir
        </h4>
        <button id="toggleFilter" class="text-xs bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-700 transition flex items-center gap-1.5 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter Data
        </button>
    </div>

    <!-- Form Filter (Drawer Modal / Toggle) -->
    <form method="GET" action="{{ route('dashboard') }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-md space-y-3 mb-5 transition-all" id="filterForm" style="display: none;">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Dari Tanggal</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-slate-200">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Sampai Tanggal</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-slate-200">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Kata Kunci</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-slate-200" placeholder="Cari: Gaji, Makan, dll">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Kategori</label>
                <select name="category_id" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-slate-200">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-xl text-xs font-semibold transition">
                Terapkan Filter
            </button>
            <a href="{{ route('dashboard') }}" class="px-4 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 py-2 rounded-xl text-xs font-semibold transition text-center">
                Reset
            </a>
        </div>
    </form>

    <!-- List Transaksi -->
    <div class="space-y-3">
        @foreach ($recentTransactions as $trx)
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-3.5 rounded-2xl shadow-sm flex items-center justify-between gap-4 w-full hover:shadow-md transition">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="p-2.5 rounded-xl shrink-0 {{ $trx->type === 'income' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' }}">
                        @if($trx->type === 'income')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-slate-800 dark:text-slate-100 text-xs sm:text-sm truncate">{{ $trx->title }}</p>
                            @if(isset($trx->category))
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-medium shrink-0">
                                    {{ $trx->category->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ \Carbon\Carbon::parse($trx->date ?? $trx->created_at)->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <p class="{{ $trx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }} font-bold text-xs sm:text-sm whitespace-nowrap">
                        {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @endforeach

        @if ($recentTransactions->isEmpty())
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center">
                <span class="text-3xl">🍃</span>
                <p class="text-xs text-slate-400 mt-2">Belum ada transaksi recorded.</p>
            </div>
        @endif
    </div>

</div>

<script>
    // Data JSON dari Controller
    const chartData = @json($chartData);

    // Filter Form Toggle
    const toggleFilterBtn = document.getElementById('toggleFilter');
    if (toggleFilterBtn) {
        toggleFilterBtn.addEventListener('click', function() {
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.style.display = filterForm.style.display === 'none' ? 'block' : 'none';
            }
        });
    }

    // Balance Visibility Toggle
    let balanceVisible = true;
    function toggleBalance() {
        balanceVisible = !balanceVisible;
        const balanceAmount = document.getElementById('balanceAmount');
        const incomeAmount = document.getElementById('incomeAmount');
        const expenseAmount = document.getElementById('expenseAmount');

        const realBalance = "Rp {{ number_format($balance, 0, ',', '.') }}";
        const realIncome = "Rp {{ number_format($totalIncome, 0, ',', '.') }}";
        const realExpense = "Rp {{ number_format($totalExpense, 0, ',', '.') }}";

        if (balanceAmount) balanceAmount.innerText = balanceVisible ? realBalance : '••••••••';
        if (incomeAmount) incomeAmount.innerText = balanceVisible ? realIncome : '••••••••';
        if (expenseAmount) expenseAmount.innerText = balanceVisible ? realExpense : '••••••••';

        const eyeIcon = document.getElementById('eyeIcon');
        if (eyeIcon) {
            eyeIcon.innerHTML = balanceVisible
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 012.284-3.668M9.88 9.88a3 3 0 104.24 4.24M15 12a3 3 0 00-3-3M3 3l18 18"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>`;
        }
    }

    // Chart Global Theme Settings
    function getChartTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            textColor: isDark ? '#94a3b8' : '#64748b',
            gridColor: isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)',
        };
    }

    var trendChartInstance = null;
    var categoryChartInstance = null;
    var monthlyChartInstance = null;
    var categoryChartType = 'pie';

    function toggleCategoryChartType() {
        categoryChartType = categoryChartType === 'pie' ? 'doughnut' : 'pie';
        var btn = document.getElementById('catChartTypeBtn');
        if (btn) {
            btn.innerText = categoryChartType === 'pie' ? 'Tipe: Pie (Full)' : 'Tipe: Donut';
        }
        renderAllCharts();
    }

    function renderAllCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not yet loaded, retrying in 300ms...');
            setTimeout(renderAllCharts, 300);
            return;
        }

        var theme = getChartTheme();

        // --- CHART 1: Tren Keuangan (Line/Area) ---
        try {
            var trendEl = document.getElementById('trendChart');
            if (trendEl) {
                var ctxTrend = trendEl.getContext('2d');
                if (trendChartInstance) { trendChartInstance.destroy(); trendChartInstance = null; }

                trendChartInstance = new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: chartData.trend.labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: chartData.trend.income,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: '#10b981',
                                borderWidth: 2.5,
                            },
                            {
                                label: 'Pengeluaran',
                                data: chartData.trend.expense,
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.12)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: '#f43f5e',
                                borderWidth: 2.5,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: theme.textColor, font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: theme.textColor,
                                    font: { size: 9 },
                                    maxRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: 15,
                                },
                                grid: { color: theme.gridColor }
                            },
                            y: {
                                ticks: {
                                    color: theme.textColor,
                                    font: { size: 10 },
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + 'jt';
                                        if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                                        return value;
                                    }
                                },
                                grid: { color: theme.gridColor }
                            }
                        }
                    }
                });
            }
        } catch(e) { console.error('Trend chart error:', e); }

        // --- CHART 2: Category Breakdown (Pie / Doughnut) ---
        try {
            var catEl = document.getElementById('categoryChart');
            if (catEl && chartData.categories && chartData.categories.data && chartData.categories.data.length > 0) {
                var ctxCategory = catEl.getContext('2d');
                if (categoryChartInstance) { categoryChartInstance.destroy(); categoryChartInstance = null; }

                categoryChartInstance = new Chart(ctxCategory, {
                    type: categoryChartType,
                    data: {
                        labels: chartData.categories.labels,
                        datasets: [{
                            data: chartData.categories.data,
                            backgroundColor: [
                                '#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#06b6d4', '#8b5cf6', '#ec4899'
                            ],
                            borderWidth: 2,
                            borderColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: categoryChartType === 'doughnut' ? '70%' : 0,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { color: theme.textColor, font: { size: 10 }, boxWidth: 12 }
                            }
                        }
                    }
                });
            }
        } catch(e) { console.error('Category chart error:', e); }

        // --- CHART 3: Arus Kas Bulanan (Bar) ---
        try {
            var monthlyEl = document.getElementById('monthlyChart');
            if (monthlyEl) {
                var ctxMonthly = monthlyEl.getContext('2d');
                if (monthlyChartInstance) { monthlyChartInstance.destroy(); monthlyChartInstance = null; }

                monthlyChartInstance = new Chart(ctxMonthly, {
                    type: 'bar',
                    data: {
                        labels: chartData.monthly.labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: chartData.monthly.income,
                                backgroundColor: '#10b981',
                                borderRadius: 6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: chartData.monthly.expense,
                                backgroundColor: '#f43f5e',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { color: theme.textColor, font: { size: 11 } }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: theme.textColor, font: { size: 10 } },
                                grid: { display: false }
                            },
                            y: {
                                ticks: { color: theme.textColor, font: { size: 10 } },
                                grid: { color: theme.gridColor }
                            }
                        }
                    }
                });
            }
        } catch(e) { console.error('Monthly chart error:', e); }
    }

    // Expose renderAllCharts globally for the layout's toggleTheme function
    window.renderAllCharts = renderAllCharts;

    // Real-time Clock following user's browser timezone
    function updateLiveClock() {
        const clockEl = document.getElementById('userLiveClock');
        if (!clockEl) return;

        const now = new Date();
        const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };

        const dateStr = now.toLocaleDateString('id-ID', optionsDate);
        const timeStr = now.toLocaleTimeString('id-ID', optionsTime);

        // Timezone abbreviation (e.g. WIB, WITA, WIT or GMT offset)
        const timeZoneName = new Intl.DateTimeFormat('id-ID', { timeZoneName: 'short' })
            .formatToParts(now)
            .find(p => p.type === 'timeZoneName')?.value || '';

        clockEl.innerHTML = `<span>📅 ${dateStr}</span> <span class="opacity-40">•</span> <span>🕒 ${timeStr} ${timeZoneName}</span>`;
    }

    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    // Initialize charts with multiple fallback triggers
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(renderAllCharts, 100);
    }
    document.addEventListener('DOMContentLoaded', function() { setTimeout(renderAllCharts, 100); });
    window.addEventListener('load', function() { setTimeout(renderAllCharts, 200); });
</script>

<!-- Modals Inclusion -->
<x-modals.income-modal :categories="$categories" :wallets="$wallets" />
<x-modals.expense-modal :categories="$categories" :wallets="$wallets" :debts="$debts" />
<x-modals.wallet-modal />
<x-modals.debt-modal :categories="$categories" />
@endsection

