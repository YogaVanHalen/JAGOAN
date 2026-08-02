@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 py-6 bg-gray-50 min-h-screen">

<a href="{{ route('goals.index') }}" class="inline-flex items-center text-sm text-teal-600 hover:underline mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Kembali
</a>

    <div class="bg-white p-6 rounded-2xl shadow-md mb-6">
        <h2 class="text-3xl font-bold text-teal-700">{{ $goal->title }}</h1>
        <p class="text-sm text-gray-500 mt-2">{{ $goal->description }}</p>
        <p class="mt-4 text-sm text-gray-400">Target: Rp {{ number_format($goal->target, 0, ',', '.') }}</p>
        <p class="text-sm text-gray-400">Progress: Rp {{ number_format($goal->progress, 0, ',', '.') }}</p>

        @if ($goal->progress >= $goal->target)
        <span class="inline-block px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full mt-4">
            🎉 Goal Tercapai!
        </span>
        @endif
    </div>
    
    @if ($goal->progress < $goal->target)
    <div class="bg-white p-6 rounded-2xl shadow-md mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Update Progress</h2>
        <form action="{{ route('goals.updateProgress', $goal->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="progress_update" class="block text-sm font-medium text-gray-700">Tambah Nominal</label>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500 text-sm">Rp</span>
                    <input type="text" name="progress_update" id="progress_update"
                           class="mt-1 block w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                           onkeyup="formatCurrencyInput(this);" 
                           required>
                </div>
                <p class="mt-1 text-xs text-gray-500">Masukkan nominal tanpa titik atau koma.</p>
            </div>
            <button type="submit" class="bg-teal-600 text-white py-2 px-6 rounded-xl hover:bg-teal-700 transition duration-300">
                Simpan Progress
            </button>
        </form>
    </div>
    
    <script>
        function formatCurrencyInput(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = formatRupiah(value);
        }
    
        function formatRupiah(angka) {
            const number_string = angka.replace(/[^,\d]/g, '').toString();
            const split = number_string.split(',');
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
    
            return rupiah;
        }
    </script>
    @endif    

    <h2 class="text-2xl font-semibold text-teal-700 mb-4">Riwayat Transaksi</h2>

    <div class="space-y-4">
        @if ($transactions->isEmpty())
        <div class="text-center text-gray-400 mt-20">
            <p class="text-lg font-semibold">Belum ada transaksi 😢</p>
        </div>
        @else
        @foreach ($transactions as $transaction)
        <div class="bg-white px-3 py-2 rounded-lg border border-gray-100 flex justify-between items-center text-xs">
            <div>
                <p class="text-gray-500">{{ $transaction->title }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M Y') }}</p>
            </div>
            <p class="text-gray-400">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
        </div>
        @endforeach        
        @endif
    </div>

</div>
@endsection
