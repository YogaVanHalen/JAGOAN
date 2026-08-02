@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24 space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">💸 Pengeluaran</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Daftar riwayat belanja dan pembayaran cicilan Anda</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('export.expenses') }}" class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <span>📥</span> Export Excel
            </a>
            <button onclick="openModal('expenseModal')" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
                <span>➕</span> Catat Pengeluaran
            </button>
        </div>
    </div>

    <!-- Filter Card Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-3xl shadow-sm">
        <form method="GET" action="{{ route('expense.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <!-- Filter Periode -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                    📅 Periode Waktu
                </label>
                <select name="period" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 font-semibold focus:ring-2 focus:ring-rose-500">
                    <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="daily" {{ ($period ?? '') === 'daily' ? 'selected' : '' }}>Harian (Hari Ini)</option>
                    <option value="weekly" {{ ($period ?? '') === 'weekly' ? 'selected' : '' }}>Mingguan (Minggu Ini)</option>
                    <option value="monthly" {{ ($period ?? '') === 'monthly' ? 'selected' : '' }}>Bulanan (Bulan Ini)</option>
                    <option value="yearly" {{ ($period ?? '') === 'yearly' ? 'selected' : '' }}>Tahunan (Tahun Ini)</option>
                </select>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                    🏷️ Kategori
                </label>
                <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 font-semibold focus:ring-2 focus:ring-rose-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)($categoryId ?? '') === (string)$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Filter Button -->
            <div>
                @if(($period ?? 'all') !== 'all' || !empty($categoryId))
                    <a href="{{ route('expense.index') }}" class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-xl text-xs font-bold hover:bg-rose-100 transition">
                        <span>🔄</span> Reset Filter
                    </a>
                @else
                    <div class="text-[11px] text-slate-400 italic py-2 text-center sm:text-left">
                        Filter aktif secara otomatis.
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Compact Total Banner -->
    <div class="bg-gradient-to-r from-rose-700 via-rose-800 to-slate-900 text-white px-4 py-3 rounded-2xl shadow-md border border-rose-500/30 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-500/30 text-white flex items-center justify-center font-bold text-base shrink-0 border border-rose-400/30">
                💸
            </div>
            <div>
                <span class="text-[11px] uppercase font-bold tracking-wider text-rose-200 block leading-tight">Total Pengeluaran</span>
                <h2 class="text-lg sm:text-xl font-black text-white leading-tight mt-0.5">
                    -Rp {{ number_format($filteredTotal ?? 0, 0, ',', '.') }}
                </h2>
            </div>
        </div>
        <div class="flex items-center gap-1.5 bg-slate-800/90 text-white px-3 py-1.5 rounded-xl border border-slate-700/80 shrink-0 text-xs font-extrabold shadow-sm">
            <span>📊</span>
            <span>{{ $expenses->count() }} Data</span>
        </div>
    </div>

    <!-- Expense List -->
    <div class="space-y-3">
        @foreach($expenses as $expense)
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between gap-4 w-full">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold shrink-0">
                        ↑
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base truncate">{{ $expense->title }}</h3>
                            @if($expense->category)
                                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-md font-medium border border-slate-200/60 dark:border-slate-700 shrink-0">
                                    {{ $expense->category->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">
                            📅 {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
                            @if($expense->wallet)
                                • <span class="text-indigo-600 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-950/80 px-2 py-0.5 rounded-md border border-indigo-200/60 dark:border-indigo-800/60">💳 {{ $expense->wallet->name }}</span>
                            @endif
                            @if($expense->debt)
                                • <span class="text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-950/80 px-2 py-0.5 rounded-md border border-rose-200/60 dark:border-rose-800/60">📉 {{ $expense->debt->name }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4 shrink-0 text-right">
                    <span class="text-sm sm:text-base font-extrabold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                        -Rp {{ number_format($expense->amount, 0, ',', '.') }}
                    </span>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('expense.edit', $expense->id) }}" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition border border-indigo-200/60 dark:border-indigo-800/60 leading-none">
                            Edit
                        </a>
                        <form action="{{ route('expense.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi pengeluaran ini?');" class="inline-flex items-center m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900 transition border border-rose-200/60 dark:border-rose-800/60 leading-none">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if($expenses->isEmpty())
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center">
                <span class="text-3xl">☕</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-200 mt-2">Belum Ada Pengeluaran Sesuai Filter</h3>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter kategori/periode atau catat transaksi baru.</p>
                <button onclick="openModal('expenseModal')" class="inline-block mt-4 text-xs bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl font-semibold transition">
                    + Catat Pengeluaran Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<x-modals.expense-modal :categories="$categories" :wallets="$wallets" :debts="$debts" />
@endsection
