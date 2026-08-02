@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">➕ Catat Hutang & Pinjaman Baru</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tambahkan data KPR, Kartu Kredit, atau cicilan lainnya</p>
        </div>
        <a href="{{ route('debts.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('debts.store') }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Hutang / Pinjaman *</label>
            <input type="text" name="name" required placeholder="Contoh: KPR Rumah BSD, Kartu Kredit Mandiri, Kredit Mobil BCA" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Pinjaman *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pengeluaran Terkait (Opsional)</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    <option value="">-- Pilih Kategori Cicilan/Hutang --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Hutang Awal (Rp) *</label>
                <input type="text" name="initial_amount" required placeholder="Contoh: 120.000.000" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Hutang Saat Ini (Rp) *</label>
                <input type="text" name="remaining_amount" required placeholder="Contoh: 90.000.000" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cicilan / Bulan (Rp) *</label>
                <input type="text" name="monthly_installment" required placeholder="Contoh: 3.000.000" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Tenor (Bulan) *</label>
                <input type="number" name="tenor_months" value="12" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Tenor (Bulan) *</label>
                <input type="number" name="remaining_tenor_months" value="12" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo (1 - 31) *</label>
                <input type="number" name="due_day" min="1" max="31" value="10" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Label</label>
                <input type="color" name="color" value="#ef4444" class="h-10 w-20 p-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                Simpan Catatan Hutang
            </button>
        </div>
    </form>
</div>
@endsection
