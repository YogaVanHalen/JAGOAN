@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">➖ Tambah Pengeluaran</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Catat transaksi belanja atau pembayaran cicilan</p>
        </div>
        <a href="{{ route('expense.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('expense.store') }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Keperluan *</label>
            <input type="text" name="title" value="{{ request('title') }}" required placeholder="Contoh: Belanja Bulanan, Bayar KPR" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Pengeluaran (Rp) *</label>
                <input type="text" name="amount" value="{{ request('amount') }}" required placeholder="Contoh: 150.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sumber Pembayaran (Rekening/Kartu)</label>
                <select name="wallet_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Sumber Uang</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                            {{ $w->name }} {{ $w->is_credit ? '[💳 Kredit - Menambah Hutang]' : '[💵 Cash/Tabungan]' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Untuk Bayar Cicilan / Hutang</label>
                <select name="debt_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Bukan Pembayaran Cicilan</option>
                    @foreach($debts as $debt)
                        <option value="{{ $debt->id }}" {{ request('debt_id') == $debt->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                            {{ $debt->name }} (Sisa: Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pengeluaran</label>
            <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Kategori (Opsional)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                Simpan Pengeluaran
            </button>
        </div>
    </form>
</div>
@endsection
