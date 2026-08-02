@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-28 max-w-2xl mx-auto">
    <!-- Header Title -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight flex items-center gap-2.5">
                <span class="text-indigo-600 dark:text-indigo-400">⚙️</span> Pengaturan & Fitur
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pusat kendali akun, anggota keluarga, kategori, & backup data Anda</p>
        </div>
    </div>

    <!-- User Profile Summary Card -->
    <div class="bg-gradient-to-br from-indigo-600 to-slate-900 dark:from-slate-900 dark:to-slate-800 text-white rounded-3xl p-6 shadow-xl mb-6 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-extrabold text-xl text-white border border-white/20 shadow-md shrink-0">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold truncate text-white">{{ $user->name }}</h2>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-white/20 text-white border border-white/20 uppercase">
                        {{ $user->role ?? 'user' }}
                    </span>
                </div>
                <p class="text-xs text-indigo-200/90 truncate mt-0.5">@ {{ $user->username ?? 'user' }} • {{ $user->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="bg-white/15 hover:bg-white/25 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition border border-white/20 backdrop-blur-sm shrink-0">
                Edit
            </a>
        </div>
    </div>

    <!-- Main Settings Menu Section -->
    <div class="space-y-3">
        <!-- 1. Anggota & Share Wallet -->
        <a href="{{ route('family.members') }}" class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-500/40 dark:hover:border-indigo-500/40 transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    👥
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Anggota & Share Wallet</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Buat sub-user & bagikan dompet keluarga</p>
                </div>
            </div>
            <span class="text-slate-400 text-sm font-bold group-hover:translate-x-1 transition-transform">→</span>
        </a>

        <!-- 2. Kelola Kategori -->
        <a href="{{ route('categories.index') }}" class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-amber-500/40 dark:hover:border-amber-500/40 transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    🏷️
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Kelola Kategori</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur kategori pemasukan & pengeluaran</p>
                </div>
            </div>
            <span class="text-slate-400 text-sm font-bold group-hover:translate-x-1 transition-transform">→</span>
        </a>

        <!-- 3. Backup & Import Data -->
        <a href="{{ route('export.index') }}" class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-sky-500/40 dark:hover:border-sky-500/40 transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950/80 text-sky-600 dark:text-sky-400 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    📥
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Backup & Import Data (Excel/CSV)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">1-klik unduh backup Master Excel / upload data</p>
                </div>
            </div>
            <span class="text-slate-400 text-sm font-bold group-hover:translate-x-1 transition-transform">→</span>
        </a>

        <!-- 4. Edit Profil -->
        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-500/40 dark:hover:border-indigo-500/40 transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    👤
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Edit Profil & Akun</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ubah nama, username, email, atau password</p>
                </div>
            </div>
            <span class="text-slate-400 text-sm font-bold group-hover:translate-x-1 transition-transform">→</span>
        </a>

        <!-- Admin Panel Link (if Admin) -->
        @if($user && $user->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between p-4 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 rounded-2xl shadow-sm hover:shadow-md transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    👑
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Admin Panel System</h3>
                    <p class="text-xs text-amber-600/80 dark:text-amber-400/80">Manajemen pengguna & hak akses sistem</p>
                </div>
            </div>
            <span class="text-amber-500 text-sm font-bold group-hover:translate-x-1 transition-transform">→</span>
        </a>
        @endif

        <!-- 5. Theme Toggle Option -->
        <button onclick="toggleTheme()" type="button" class="w-full text-left flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition group">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition-transform">
                    🌙
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tema Aplikasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ganti tampilan antara Mode Terang / Gelap</p>
                </div>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700">
                Ganti Tema
            </span>
        </button>

        <!-- 6. Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 p-3.5 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-950/60 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-rose-600 dark:text-rose-400 font-bold text-sm transition">
                <span>🚪</span> Keluar dari Akun (Logout)
            </button>
        </form>
    </div>
</div>
@endsection
