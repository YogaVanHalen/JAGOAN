@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">✏️ Edit Hutang & Pinjaman</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perbarui rincian {{ $debt->name }}</p>
        </div>
        <a href="{{ route('debts.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('debts.update', $debt->id) }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Hutang / Pinjaman *</label>
            <input type="text" name="name" value="{{ old('name', $debt->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Pinjaman *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ $debt->type === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pengeluaran Terkait (Opsional)</label>
                <select name="category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    <option value="">-- Pilih Kategori Cicilan/Hutang --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $debt->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Hutang Awal (Rp) *</label>
                <input type="text" name="initial_amount" value="{{ old('initial_amount', number_format($debt->initial_amount, 0, '', '')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Hutang Saat Ini (Rp) *</label>
                <input type="text" name="remaining_amount" value="{{ old('remaining_amount', number_format($debt->remaining_amount, 0, '', '')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cicilan / Bulan (Rp) *</label>
                <input type="text" name="monthly_installment" value="{{ old('monthly_installment', number_format($debt->monthly_installment, 0, '', '')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Tenor (Bulan) *</label>
                <input type="number" name="tenor_months" value="{{ old('tenor_months', $debt->tenor_months) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Tenor (Bulan) *</label>
                <input type="number" name="remaining_tenor_months" value="{{ old('remaining_tenor_months', $debt->remaining_tenor_months) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo (1 - 31) *</label>
                <input type="number" name="due_day" min="1" max="31" value="{{ old('due_day', $debt->due_day) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Bunga Custom (% / Bln)</label>
                <input type="number" step="0.01" name="interest_rate_percent" value="{{ old('interest_rate_percent', $debt->interest_rate_percent) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Label</label>
                <input type="color" name="color" value="{{ old('color', $debt->color ?? '#ef4444') }}" class="h-10 w-20 p-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
            </div>
        </div>

        <div class="flex items-center justify-between bg-rose-50/50 dark:bg-rose-950/30 p-3 rounded-xl border border-rose-200/50 dark:border-rose-800/40">
            <div class="leading-tight">
                <label for="auto_accrue_edit" class="text-xs font-bold text-slate-800 dark:text-slate-200 block cursor-pointer">📈 Hitung & Akumulasikan Bunga Otomatis</label>
                <span class="text-[10px] text-slate-400">Bunga dihitung otomatis H+1 setelah tanggal jatuh tempo.</span>
            </div>
            <input type="checkbox" name="auto_accrue_interest" id="auto_accrue_edit" value="1" {{ old('auto_accrue_interest', $debt->auto_accrue_interest) ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded cursor-pointer">
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
