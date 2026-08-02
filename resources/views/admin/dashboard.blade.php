@extends('layouts.app')

@section('content')
<div class="px-4 py-6 sm:px-6 space-y-6">
    <!-- Header Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 rounded-3xl shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-widest bg-amber-400 text-slate-900 rounded-full">
                    Admin Panel
                </span>
            </div>
            <h2 class="text-2xl font-black tracking-tight">Ringkasan Sistem & Pengguna</h2>
            <p class="text-indigo-200 text-xs mt-1">Pantau statistik aktivitas seluruh pengguna aplikasi ANNME Money (JAGOAN).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users') }}" class="px-4 py-2.5 bg-white text-indigo-900 font-bold text-xs rounded-2xl hover:bg-indigo-50 transition shadow-sm flex items-center gap-2">
                <span>👥</span> Kelola Pengguna
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold mb-3">
                👥
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Total User</p>
            <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">{{ number_format($totalUsers) }}</h3>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold mb-3">
                💳
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Total Dompet</p>
            <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">{{ number_format($totalWallets) }}</h3>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl font-bold mb-3">
                📈
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Total Pemasukan</p>
            <h3 class="text-lg sm:text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1 truncate">
                Rp {{ number_format($totalIncomes, 0, ',', '.') }}
            </h3>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-bold mb-3">
                📉
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Total Pengeluaran</p>
            <h3 class="text-lg sm:text-xl font-black text-rose-600 dark:text-rose-400 mt-1 truncate">
                Rp {{ number_format($totalExpenses, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- User Terbaru Registered -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Pengguna Terbaru Mendaftar</h3>
            <a href="{{ route('admin.users') }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                Lihat Semua ({{ $totalUsers }}) →
            </a>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($recentUsers as $user)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                {{ $user->name }}
                                @if($user->role === 'admin')
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold bg-amber-400/20 text-amber-600 dark:text-amber-400 border border-amber-400/30 rounded-md">ADMIN</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium">
                        {{ $user->created_at ? $user->created_at->diffForHumans() : '-' }}
                    </span>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada pengguna terdaftar.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
