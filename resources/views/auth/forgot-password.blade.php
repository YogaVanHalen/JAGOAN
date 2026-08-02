@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 sm:p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Forgot Password?</h2>
            <p class="text-sm text-gray-600 text-center mb-6">
                No problem. Enter your email and we'll send you a link to reset your password.
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        Send Password Reset Link
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">
                    Back to Login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection