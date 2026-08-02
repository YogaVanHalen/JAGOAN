@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">📉 Hutang & Pinjaman</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau cicilan KPR, KTA, Kartu Kredit, dan Paylater</p>
        </div>
        <button onclick="openModal('debtModal')" class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
            <span>➕</span> Catat Hutang Baru
        </button>
    </div>

    <!-- Summary Banner -->
    <div class="bg-gradient-to-r from-rose-600 via-rose-700 to-amber-700 dark:from-slate-900 dark:to-rose-950 p-6 rounded-3xl shadow-lg text-white mb-6 border border-white/10">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider opacity-80">Total Sisa Hutang</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Rp {{ number_format($debts->sum('remaining_amount'), 0, ',', '.') }}</h2>
            </div>
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider opacity-80">Cicilan Per Bulan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Rp {{ number_format($debts->sum('monthly_installment'), 0, ',', '.') }}</h2>
            </div>
        </div>
        <p class="text-xs opacity-75 mt-3">✨ Transaksi via Kartu Kredit / Paylater otomatis menambah pokok hutang, sedangkan pembayaran cicilan KPR/KTA/KKB akan mengurangi sisa hutang & tenor.</p>
    </div>

    <!-- Debts List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($debts as $debt)
            @php
                $isRevolving = in_array(strtolower($debt->type), ['kartu kredit', 'visa', 'mastercard', 'amex', 'jcb', 'paylater', 'pinjol', 'paylater / pinjol']);
                $paidAmount = max(0, $debt->initial_amount - $debt->remaining_amount);
                $percentage = ($debt->initial_amount > 0 && !$isRevolving) ? min(100, round(($paidAmount / $debt->initial_amount) * 100)) : 0;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg text-white" style="background-color: {{ $debt->color ?? '#ef4444' }}">
                            {{ $debt->type }}
                        </span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('debts.edit', $debt->id) }}" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition border border-indigo-200/50 dark:border-indigo-800/40 leading-none">Edit</a>
                            <form action="{{ route('debts.destroy', $debt->id) }}" method="POST" onsubmit="return confirm('Hapus catatan hutang ini?');" class="inline-flex items-center m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition border border-rose-200/50 dark:border-rose-800/40 leading-none">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg mb-1">{{ $debt->name }}</h3>
                    <div class="flex items-center gap-1.5 flex-wrap mb-1">
                        @if($debt->category)
                            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded font-medium">Kategori: {{ $debt->category->name }}</span>
                        @endif
                        @if($debt->auto_accrue_interest)
                            <span class="text-[10px] bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 font-bold px-2 py-0.5 rounded-md border border-amber-300/40">
                                📈 Bunga {{ $debt->interest_rate_percent }}% (Akumulasi H+1)
                            </span>
                        @endif
                        @if($debt->goal)
                            <a href="{{ route('goals.show', $debt->goal->id) }}" class="text-[10px] bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-bold px-2 py-0.5 rounded-md border border-indigo-300/40 hover:underline">
                                🎯 Goal Linked
                            </a>
                        @else
                            <form action="{{ route('debts.convertToGoal', $debt->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-[10px] bg-indigo-50 dark:bg-indigo-900/40 hover:bg-indigo-100 dark:hover:bg-indigo-800/60 text-indigo-600 dark:text-indigo-300 font-bold px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-700 transition">
                                    + 🎯 Jadikan Goal
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
                        <div class="bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block text-[10px]">Pokok Hutang Saat Ini</span>
                            <span class="font-extrabold text-rose-600 dark:text-rose-400 text-sm">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block text-[10px]">Cicilan / Tagihan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">Rp {{ number_format($debt->monthly_installment, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Progress Bar for Fixed Tenor Loans (KPR/KTA/KKB) -->
                    @if(!$isRevolving)
                        <div class="mt-4">
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-500 dark:text-slate-400">Progres Pelunasan</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $percentage }}% Lunas</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-400 mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span>⏳ Sisa Tenor: <strong>{{ $debt->remaining_tenor_months }} Bulan</strong> (dari {{ $debt->tenor_months }} bln)</span>
                            <span>📅 Jatuh Tempo: Tgl <strong>{{ $debt->due_day }}</strong></span>
                        </div>
                    @else
                        <!-- Revolving Credit Card / Paylater Info -->
                        <div class="mt-3 text-[11px] text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-2 flex justify-between">
                            <span>💳 Kartu Kredit / Paylater Berjalan</span>
                            <span>📅 Jatuh Tempo: Tgl <strong>{{ $debt->due_day }}</strong></span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-2">
                    <a href="{{ route('expense.create', ['debt_id' => $debt->id, 'amount' => $debt->monthly_installment, 'title' => 'Bayar Tagihan ' . $debt->name]) }}" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition shadow-sm">
                        <span>💳</span> Bayar Tagihan Ini
                    </a>
                </div>
            </div>
        @endforeach

                @if($debts->isEmpty())
            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center">
                <span class="text-3xl">🎉</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-200 mt-2">Tidak Ada Catatan Hutang / Pinjaman</h3>
                <p class="text-xs text-slate-400 mt-1">Bagus sekali! Atau silakan tambahkan Kartu Kredit / Paylater atau KPR untuk mulai pemantauan.</p>
                <button onclick="openModal('debtModal')" class="inline-block mt-4 text-xs bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl font-semibold transition">
                    + Catat Hutang Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<x-modals.debt-modal :categories="$categories" />
@endsection
