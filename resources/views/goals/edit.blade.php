@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 py-6 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6 text-teal-700">✏️ Edit Goal</h1>
    <a href="{{ route('goals.index') }}" class="inline-flex items-center text-sm text-teal-600 hover:underline mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
    </a>
    <form action="{{ route('goals.update', $goal->id) }}" method="POST" class="bg-white p-6 rounded-2xl shadow-md space-y-5" onsubmit="formatAndSubmit()">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Goal</label>
            <input type="text" name="title" id="title"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500"
                value="{{ $goal->title }}" required>
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">{{ $goal->description }}</textarea>
        </div>

        <div>
            <label for="target" class="block text-sm font-semibold text-gray-700 mb-1">Target Nominal</label>
            <input type="text" name="target" id="target"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500"
                value="{{ number_format($goal->target, 0, ',', '.') }}" required min="1">
        </div>

        <div class="flex justify-between">
            <button type="submit"
                class="bg-teal-600 text-white px-6 py-2 rounded-xl hover:bg-teal-700 transition duration-300">
                Update Goal
            </button>
        </div>
    </form>
</div>

<script>
    const targetInput = document.getElementById('target');

    // Function to format input as Rupiah
    function formatRupiah(angka, prefix = 'Rp ') {
        let number_string = angka.replace(/[^,\d]/g, '').toString();
        let split = number_string.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        if (split[1] != undefined) {
            rupiah = rupiah + ',' + split[1];
        }

        return prefix + rupiah;
    }

    targetInput.addEventListener('keyup', function(e) {
        e.target.value = formatRupiah(this.value);
    });

    // Function to remove 'Rp' and '.' before submitting the form
    function formatAndSubmit() {
        // Remove 'Rp' and '.' characters before submitting
        const targetValue = targetInput.value.replace(/[^0-9]/g, '');
        targetInput.value = targetValue;
    }

    // Set initial value to formatted Rupiah if there's any preset value
    if (targetInput.value) {
        targetInput.value = formatRupiah(targetInput.value);
    }
</script>
@endsection
