@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">➕ Tambah Pemasukan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tambahkan sumber penghasilan ke dompet Anda</p>
        </div>
        <a href="{{ route('income.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('income.store') }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Sumber Pemasukan *</label>
            <input type="text" name="title" required placeholder="Contoh: Gaji Bulanan" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Pemasukan (Rp) *</label>
                <input type="text" name="amount" required placeholder="Contoh: 10.000.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal *</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pemasukan</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Kategori (Opsional)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masuk ke Rekening / E-Wallet</label>
                <select name="wallet_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Rekening Tujuan</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $wallet->name }} ({{ $wallet->bank_name }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                Simpan Pemasukan
            </button>
        </div>
    </form>
</div>
@endsection
