@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 py-8 bg-gradient-to-br from-indigo-100 via-white to-white min-h-screen">
    <div class="max-w-2xl mx-auto bg-white bg-opacity-90 backdrop-blur-sm p-8 rounded-3xl shadow-xl border border-indigo-100 animate-fade-in">
        <h1 class="text-3xl font-bold mb-6 text-indigo-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.657 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Edit Profil
        </h1>

        @if(session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 border border-green-300">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-600">Nama</label>
                <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                       class="mt-2 w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
                @error('name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-600">Username (Untuk Login)</label>
                <input id="username" name="username" type="text" required value="{{ old('username', $user->username) }}"
                       class="mt-2 w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
                @error('username')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                       class="mt-2 w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password Baru</label>
                <input id="password" name="password" type="password" placeholder="Kosongkan jika tidak ingin ubah"
                       class="mt-2 w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="mt-2 w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-400 focus:outline-none shadow-sm">
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:bg-indigo-700 transition duration-300">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <form action="{{ route('profile.destroy') }}" method="POST"
              onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak bisa dibatalkan.')" class="mt-10 text-center">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-red-600 hover:underline text-sm font-medium">
                Hapus Akun
            </button>
        </form>
    </div>
</div>
@endsection