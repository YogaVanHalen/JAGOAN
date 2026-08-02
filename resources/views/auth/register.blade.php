@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
  <div class="w-full max-w-md bg-white rounded-xl shadow-md overflow-hidden">
    <div class="p-6 sm:p-8">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create Your Account</h2>

      @if(session('status'))
          <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ session('status') }}
          </div>
      @endif
      @if(session('error'))
          <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ session('error') }}
          </div>
      @endif

      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Contoh: Joko Widodo" />
          @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username (Untuk Login)</label>
          <input id="username" type="text" name="username" value="{{ old('username') }}" required
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Contoh: joko" />
          @error('username')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input id="password" type="password" name="password" required
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition pr-10" />
                 <button type="button"
                  class="absolute top-6 inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                  onclick="togglePassword('password', 'eyeIcon')">
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path id="eyeOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path id="eyeOpen2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="relative">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input id="password_confirmation" type="password" name="password_confirmation" required
                 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition pr-10" />
                 <button type="button"
                  class="absolute top-6 inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                  onclick="togglePassword('password_confirmation', 'eyeIconConfirm')">
            <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path id="eyeOpenConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path id="eyeOpen2Confirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
        @error('password_confirmation')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <!-- Honeypot Invisible Bot Trap Field -->
        <input type="text" name="website_url" id="website_url" style="display:none !important; position:absolute !important; left:-9999px !important;" tabindex="-1" autocomplete="off">

        <!-- Math Security Challenge (Anti-Bot Captcha) -->
        <div class="bg-indigo-50/70 p-3.5 rounded-xl border border-indigo-200/80">
          <label for="captcha" class="block text-xs font-bold text-indigo-900 mb-1 flex items-center gap-1.5">
            <span>🛡️ Verifikasi Keamanan:</span> Berapa {{ $num1 ?? 3 }} + {{ $num2 ?? 5 }} ?
          </label>
          <input id="captcha" type="number" name="captcha" required placeholder="Ketik jawaban angka"
                 class="w-full px-3.5 py-2 bg-white border border-indigo-200 rounded-lg text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
          @error('captcha')
            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <button type="submit"
                  class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
            Sign Up
          </button>
        </div>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Log in</a>
      </p>
    </div>
  </div>

  <script>
    function togglePassword(inputId, iconId) {
      const input = document.getElementById(inputId);
      const eye = document.getElementById(iconId);
      if (input.type === 'password') {
        input.type = 'text';
        eye.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.435M6.257 6.257A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.581 3.036M3 3l18 18"/>`;
      } else {
        input.type = 'password';
        eye.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>`;
      }
    }
  </script>
</div>
@endsection
