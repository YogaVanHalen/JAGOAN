@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">✏️ Edit Rekening / Kartu Kredit</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perbarui rincian {{ $wallet->name }}</p>
        </div>
        <a href="{{ route('wallets.index') }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('wallets.update', $wallet->id) }}" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <!-- Toggle Credit Card / Paylater -->
        <div class="bg-slate-50 dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
            <div>
                <label class="font-bold text-sm text-slate-800 dark:text-slate-100 block">💳 Ini Adalah Kartu Kredit / Paylater / Pinjol</label>
                <p class="text-[11px] text-slate-400">Setiap transaksi via kartu ini akan otomatis menambah Pokok Hutang pada menu Hutang & Pinjaman.</p>
            </div>
            <input type="checkbox" name="is_credit" id="is_credit" value="1" {{ $wallet->is_credit ? 'checked' : '' }} onchange="toggleCreditFields(this.checked)" class="w-5 h-5 text-rose-600 rounded cursor-pointer">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rekening / Akun *</label>
            <input type="text" name="name" value="{{ old('name', $wallet->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bank / Penyedia Layanan *</label>
                <select name="bank_name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    @foreach($bankOptions as $bank)
                        <option value="{{ $bank }}" {{ $wallet->bank_name === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rekening / Akun (Opsional)</label>
                <input type="text" name="account_number" value="{{ old('account_number', $wallet->account_number) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Conditional Credit Card Fields -->
        <div id="creditFields" class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800 {{ $wallet->is_credit ? '' : 'hidden' }}">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jaringan Kartu / Jenis Kredit</label>
                <select name="card_network" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    @foreach($networks as $network)
                        <option value="{{ $network }}" {{ $wallet->card_network === $network ? 'selected' : '' }}>{{ $network }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Limit Kredit (Rp)</label>
                    <input type="text" name="credit_limit" value="{{ old('credit_limit', number_format($wallet->credit_limit, 0, '', '')) }}" placeholder="Contoh: 15.000.000" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Bunga (% Per Bulan)</label>
                    <input type="number" step="0.01" name="interest_rate_percent" value="{{ old('interest_rate_percent', $wallet->interest_rate_percent) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
        </div>

        <div>
            <label id="balanceLabel" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                {{ $wallet->is_credit ? 'Pokok Hutang Saat Ini / Kredit Terpakai (Rp)' : 'Saldo Saat Ini (Rp)' }}
            </label>
            <input type="text" name="balance" value="{{ old('balance', number_format($wallet->balance, 0, '', '')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Label</label>
            <input type="color" name="color" value="{{ old('color', $wallet->color ?? '#6366f1') }}" class="h-10 w-20 p-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer">
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl text-sm transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
function toggleCreditFields(checked) {
    const fields = document.getElementById('creditFields');
    const label = document.getElementById('balanceLabel');
    if (checked) {
        fields.classList.remove('hidden');
        label.innerText = 'Pokok Hutang Saat Ini / Kredit Terpakai (Rp)';
    } else {
        fields.classList.add('hidden');
        label.innerText = 'Saldo Saat Ini (Rp)';
    }
}
</script>
@endsection
