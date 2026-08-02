@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">🎯 Target Finansial (Goals)</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Impian tabungan & target keuangan jangka panjang Anda</p>
        </div>
        <button onclick="openModal('goalModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
            <span>➕</span> Buat Goal Baru
        </button>
    </div>

    <!-- Goals List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($goals as $goal)
            @php
                $pct = $goal->target > 0 ? min(100, round(($goal->progress / $goal->target) * 100)) : 0;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-2xl shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg {{ $pct >= 100 ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 border border-emerald-300/40' : 'bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 border border-indigo-200/40' }}">
                                {{ $pct >= 100 ? '🎉 Goal Tercapai!' : '🎯 In Progress' }}
                            </span>
                            @if($goal->debt_id)
                                <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border border-rose-300/40">
                                    🔗 Pelunasan Hutang
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('goals.edit', $goal->id) }}" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition border border-indigo-200/50 dark:border-indigo-800/40 leading-none">
                                Edit
                            </a>
                            <a href="{{ route('goals.show', $goal->id) }}" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition border border-slate-200/50 dark:border-slate-700/50 leading-none">
                                Detail
                            </a>
                            <form action="{{ route('goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Hapus target finansial ini?');" class="inline-flex items-center m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition border border-rose-200/50 dark:border-rose-800/40 leading-none">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-lg mb-1">{{ $goal->title }}</h3>
                    @if($goal->description)
                        <p class="text-xs text-slate-400 mb-3">{{ $goal->description }}</p>
                    @endif

                    <div class="mt-4">
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-500 dark:text-slate-400">{{ $goal->debt_id ? 'Progres Pelunasan' : 'Progres Tabungan' }}</span>
                            <span class="{{ $goal->debt_id ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }} font-bold">
                                Rp {{ number_format($goal->progress, 0, ',', '.') }} / Rp {{ number_format($goal->target, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                            <div class="{{ $goal->debt_id ? 'bg-emerald-500' : 'bg-indigo-500' }} h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 text-right mt-1.5 font-medium">{{ $pct }}% {{ $goal->debt_id ? 'Lunas' : 'Tercapai' }}</p>
                    </div>
                </div>
            </div>
        @endforeach

        @if($goals->isEmpty())
            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-2xl text-center">
                <span class="text-3xl">🚀</span>
                <h3 class="font-bold text-slate-700 dark:text-slate-200 mt-2">Belum Ada Target Finansial</h3>
                <p class="text-xs text-slate-400 mt-1">Buat goal pertama Anda seperti Dana Darurat, Tabungan Rumah, atau Liburan.</p>
                <button onclick="openModal('goalModal')" class="inline-block mt-4 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-semibold transition">
                    + Buat Goal Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<x-modals.goal-modal />
@endsection
