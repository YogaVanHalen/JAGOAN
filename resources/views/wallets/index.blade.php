@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">🏦 Rekening & E-Wallet</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola dompet pribadi & dompet bersama keluarga Anda</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('family.members') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
                <span>👥</span> Anggota Keluarga & Share Wallet
            </a>
            <button onclick="openModal('walletModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
                <span>➕</span> Tambah Akun / Kartu
            </button>
        </div>
    </div>

    <!-- Overall Total Wallet Assets Banner -->
    @php
        $allWallets = $myWallets->concat($sharedWithMe);
        $totalSavings = $allWallets->where('is_credit', false)->sum('balance');
        $totalUsedCredit = $allWallets->where('is_credit', true)->sum('balance');
    @endphp
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-800 dark:from-slate-900 dark:to-indigo-950 p-6 rounded-3xl shadow-lg text-white border border-white/10">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider opacity-80">Total Tabungan & Cash</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Rp {{ number_format($totalSavings, 0, ',', '.') }}</h2>
            </div>
            <div>
                <span class="text-xs uppercase font-semibold tracking-wider opacity-80">Hutang Kredit / Paylater</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1 text-rose-300">Rp {{ number_format($totalUsedCredit, 0, ',', '.') }}</h2>
            </div>
        </div>
        <p class="text-xs opacity-75 mt-3">✨ Belanja dengan Kartu Kredit / Paylater otomatis menambah pokok hutang terpakai tanpa mengurangi saldo tabungan biasa.</p>
    </div>

    <!-- Section 1: Dompet Saya (Pribadi) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>🔒</span> Dompet Saya (Pribadi & Dibuat Sendiri)
            </h2>
            <span class="text-xs text-slate-400 font-semibold">{{ $myWallets->count() }} Dompet</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($myWallets as $wallet)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 rounded-2xl shadow-sm hover:shadow-md transition relative flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-lg text-white" style="background-color: {{ $wallet->color ?? '#6366f1' }}">
                                    {{ $wallet->bank_name }}
                                </span>
                                @if($wallet->type === 'shared' || $wallet->members->count() > 0)
                                    <span class="text-[10px] bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 font-extrabold px-2 py-0.5 rounded-md border border-purple-300/40">
                                        👥 Bersama ({{ $wallet->members->count() + 1 }} Anggota)
                                    </span>
                                @else
                                    <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 font-bold px-2 py-0.5 rounded-md">
                                        🔒 Pribadi
                                    </span>
                                @endif
                                @if($wallet->is_credit)
                                    <span class="text-[10px] bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-extrabold px-2 py-0.5 rounded-md border border-rose-300/40">
                                        💳 {{ $wallet->card_network ?? 'Kredit' }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-1 shrink-0">
                                <button onclick="openInviteModal({{ $wallet->id }}, '{{ addslashes($wallet->name) }}')" class="inline-flex items-center justify-center text-xs font-semibold px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition border border-emerald-200/50 leading-none" title="Undang Anggota">
                                    👥 Invite
                                </button>
                                <a href="{{ route('wallets.edit', $wallet->id) }}" class="inline-flex items-center justify-center text-xs font-semibold px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition border border-indigo-200/50 leading-none">Edit</a>
                                <form action="{{ route('wallets.destroy', $wallet->id) }}" method="POST" onsubmit="return confirm('Hapus dompet ini?');" class="inline-flex items-center m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center text-xs font-semibold px-2 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition border border-rose-200/50 leading-none">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base mb-1">
                            {{ $wallet->name }}
                        </h3>

                        @if($wallet->account_number)
                            <p class="text-xs text-slate-400 mb-1">No. Rek/Akun: {{ $wallet->account_number }}</p>
                        @endif

                        <!-- Members List Badge -->
                        @if($wallet->members->count() > 0)
                            <div class="mt-2 text-[11px] bg-slate-50 dark:bg-slate-800/80 p-2 rounded-xl border border-slate-100 dark:border-slate-800 space-y-1">
                                <p class="font-semibold text-slate-600 dark:text-slate-300">Anggota Ter-invite:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($wallet->members as $member)
                                        <span class="inline-flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded-lg text-[10px] text-slate-700 dark:text-slate-300">
                                            <span>👤 {{ $member->name }}</span>
                                            <form action="{{ route('wallets.removeMember', [$wallet->id, $member->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:font-bold ml-1">✕</button>
                                            </form>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($wallet->is_credit)
                            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                                <p>📊 Suku Bunga: <strong class="text-slate-700 dark:text-slate-200">{{ $wallet->interest_rate_percent }}% / bulan</strong></p>
                                @if($wallet->credit_limit > 0)
                                    <p>🔒 Limit Total: <strong>Rp {{ number_format($wallet->credit_limit, 0, ',', '.') }}</strong></p>
                                    <p>🟢 Sisa Limit: <strong class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($wallet->available_limit, 0, ',', '.') }}</strong></p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                        <span class="text-xs text-slate-400">
                            {{ $wallet->is_credit ? 'Pokok Hutang Terpakai' : 'Saldo Saat Ini' }}
                        </span>
                        <span class="text-lg font-extrabold {{ $wallet->is_credit ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-100' }}">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-2xl text-center">
                    <span class="text-2xl">🏦</span>
                    <p class="text-xs text-slate-400 mt-1">Belum ada dompet pribadi.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Dompet Bersama (Di-share dengan Anda) -->
    @if($sharedWithMe->count() > 0)
        <div class="space-y-3 pt-4 border-t border-slate-200/60 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                    <span>👥</span> Dompet Bersama (Dibagikan dengan Anda)
                </h2>
                <span class="text-xs text-slate-400 font-semibold">{{ $sharedWithMe->count() }} Dompet Shared</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($sharedWithMe as $wallet)
                    <div class="bg-white dark:bg-slate-900 border border-purple-200/80 dark:border-purple-900/40 p-4 rounded-2xl shadow-sm hover:shadow-md transition relative flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-lg text-white" style="background-color: {{ $wallet->color ?? '#8b5cf6' }}">
                                    {{ $wallet->bank_name }}
                                </span>
                                <span class="text-[10px] bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 font-extrabold px-2 py-0.5 rounded-md border border-purple-300/40">
                                    👑 Pemilik: {{ $wallet->user->name }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base mb-1">
                                {{ $wallet->name }}
                            </h3>

                            @if($wallet->account_number)
                                <p class="text-xs text-slate-400 mb-1">No. Rek/Akun: {{ $wallet->account_number }}</p>
                            @endif

                            <p class="text-[11px] text-slate-400 mt-2">
                                ℹ️ Anda diundang oleh <strong>{{ $wallet->user->name }}</strong> untuk bersama mengelola dompet ini.
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-end">
                            <span class="text-xs text-slate-400">Saldo Dompet Bersama</span>
                            <span class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Modal Invite Member -->
<div id="inviteModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>👥</span> Undang Anggota ke Dompet Bersama
            </h3>
            <button onclick="closeModal('inviteModal')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form id="inviteForm" method="POST" class="space-y-3.5 pt-4">
            @csrf
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Undang pengguna terdaftar atau buatkan akun anggota baru untuk dompet <strong id="inviteWalletName" class="text-indigo-600 dark:text-indigo-400"></strong>:
            </p>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Pengguna *</label>
                <input type="email" name="email" required placeholder="contoh: istri@email.com" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Anggota (Opsional jika baru)</label>
                <input type="text" name="name" placeholder="Contoh: Ani (Istri)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Login Anggota (Default: 123456)</label>
                <input type="password" name="password" placeholder="Kosongkan untuk default: 123456" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <p class="text-[11px] text-slate-400 italic">
                💡 Jika email belum pernah terdaftar, sistem akan otomatis membuatkan akun login baru untuk anggota ini.
            </p>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModal('inviteModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-xs shadow-sm">
                    Tambah / Undang Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<x-modals.wallet-modal />

<script>
function openInviteModal(walletId, walletName) {
    const form = document.getElementById('inviteForm');
    const nameSpan = document.getElementById('inviteWalletName');
    form.action = '/wallets/' + walletId + '/members';
    nameSpan.innerText = walletName;
    openModal('inviteModal');
}
</script>
@endsection
