@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">👥 Anggota Keluarga & Share Wallet</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola anggota (istri, anak, atau partner) yang berbagi akses ke dompet Anda</p>
        </div>
        <button onclick="openModal('addMemberModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center justify-center gap-1.5">
            <span>➕</span> Tambah / Invite Anggota Baru
        </button>
    </div>

    <!-- Summary Banner -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-indigo-800 p-6 rounded-3xl shadow-lg text-white">
        <h2 class="text-lg font-bold">ℹ️ Informasi Akses User Sharing</h2>
        <p class="text-xs sm:text-sm opacity-90 mt-1 leading-relaxed">
            Sebagai **Owner Dompet**, Anda memiliki wewenang untuk menambahkan atau mengundang anggota ke dompet Anda. Anggota yang terhubung dapat melihat saldo serta mencatat transaksi pemasukan & pengeluaran secara real-time pada dompet yang Anda bagikan.
        </p>
    </div>

    <!-- Members List -->
    <div class="space-y-4">
        <h2 class="font-bold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
            <span>👥</span> Anggota Terdaftar yang Terhubung ({{ $members->count() }})
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($members as $member)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $member->name }}</h3>
                                <p class="text-xs text-slate-400">{{ $member->email }}</p>
                            </div>
                        </div>

                        <!-- Wallets Shared with Member -->
                        <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Akses Dompet Bersama:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($member->sharedWallets as $w)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg text-white" style="background-color: {{ $w->color ?? '#6366f1' }}">
                                        💳 {{ $w->name }}
                                        <form action="{{ route('wallets.removeMember', [$w->id, $member->id]) }}" method="POST" onsubmit="return confirm('Keluarkan anggota dari dompet {{ $w->name }}?');" class="inline m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="hover:text-rose-200 font-extrabold ml-1">✕</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs text-slate-400">
                        <span>Role: Member (User)</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">● Terhubung</span>
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center space-y-3">
                    <span class="text-4xl">👥</span>
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Belum ada anggota terhubung</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Klik tombol <strong>"Tambah / Invite Anggota Baru"</strong> untuk mengundang pasangan, anak, atau rekan Anda ke dompet bersama.
                    </p>
                    <button onclick="openModal('addMemberModal')" class="inline-block bg-indigo-600 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm hover:bg-indigo-700 transition">
                        ➕ Tambah Anggota Sekarang
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah / Invite Anggota -->
<div id="addMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-6 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>👤</span> Tambah Anggota / User Sharing
            </h3>
            <button onclick="closeModal('addMemberModal')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('family.members.store') }}" class="space-y-4 pt-4">
            @csrf
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Masukkan email anggota. Jika belum terdaftar, sistem akan **otomatis membuatkan akun login baru** untuk anggota tersebut.
            </p>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Anggota *</label>
                <input type="email" name="email" required placeholder="contoh: istri@email.com" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Anggota (Opsional jika baru)</label>
                <input type="text" name="name" placeholder="Contoh: Ani (Istri)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Login (Default: 123456)</label>
                <input type="password" name="password" placeholder="Kosongkan jika default: 123456" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Dompet yang Dibagikan *</label>
                <div class="space-y-2 max-h-40 overflow-y-auto p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl">
                    @forelse($myWallets as $w)
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-200 font-medium cursor-pointer">
                            <input type="checkbox" name="wallet_ids[]" value="{{ $w->id }}" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $w->color ?? '#6366f1' }}"></span>
                            <span>{{ $w->name }} ({{ $w->bank_name }})</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada dompet. Buat dompet dulu di menu Rekening.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModal('addMemberModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-xs shadow-sm">
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
