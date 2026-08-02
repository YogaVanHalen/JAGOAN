@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 p-4 transition-colors duration-300">
  <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-xl p-8 transition-colors duration-300">
    
    <!-- Brand Header -->
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-black text-2xl shadow-md shadow-indigo-500/30 mb-3">
        J
      </div>
      <h2 class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight">Welcome Back to JAGOAN</h2>
      <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Smart Cash Flow & Debt Tracker for Family</p>
    </div>

    @if(session('status'))
      <div class="mb-4 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 p-3 rounded-xl">
        {{ session('status') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-4 text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 p-3 rounded-xl">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      <div>
        <label for="username" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Username / Email</label>
        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 transition"
               placeholder="Ketik username Anda (misal: joko)">
        @error('username')
          <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password</label>
        <div class="relative flex items-center">
          <input id="password" type="password" name="password" required
                 class="w-full px-3.5 py-2.5 pr-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 transition"
                 placeholder="••••••••">
          <button type="button" onclick="togglePassword()" class="absolute right-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
        @error('password')
          <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex items-center justify-between text-xs pt-1">
        <label class="inline-flex items-center text-slate-600 dark:text-slate-400 cursor-pointer">
          <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500">
          <span class="ml-2 font-medium">Remember me</span>
        </label>
        @if(Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
            Forgot password?
          </a>
        @endif
      </div>

      @if(config('services.cloudflare.turnstile_site_key'))
        <!-- Cloudflare Turnstile Verification Widget -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <div class="my-3 flex justify-center">
          <div class="cf-turnstile" data-sitekey="{{ config('services.cloudflare.turnstile_site_key') }}"></div>
        </div>
      @endif

      <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-600/20 transition duration-200">
        Log In
      </button>
    </form>

    <div class="flex items-center my-5">
      <hr class="flex-grow border-t border-slate-200 dark:border-slate-800">
      <span class="mx-3 text-slate-400 text-xs font-medium">Or continue with</span>
      <hr class="flex-grow border-t border-slate-200 dark:border-slate-800">
    </div>

    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 font-bold text-xs shadow-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition w-full"> 
      <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="h-4 w-4">
      Sign in with Google
    </a>

    <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
      Don’t have an account?
      <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Sign up</a>
    </p>
  </div>
</div>

<script>
  function togglePassword() {
    const pw = document.getElementById('password');
    const type = pw.type === 'password' ? 'text' : 'password';
    pw.type = type;
  }
</script>
<script>
  function isWebView() {
    const ua = navigator.userAgent || navigator.vendor || window.opera;
    return /(FBAN|FBAV|Instagram|Line|Twitter|WebView|iPhone.*Safari\/(?!.*Chrome)|Android.*(wv))/.test(ua);
  }

  document.addEventListener("DOMContentLoaded", function () {
    const googleBtn = document.querySelector('a[href*="auth/google"]');
    if (isWebView() && googleBtn) {
      googleBtn.addEventListener('click', function (e) {
        e.preventDefault();
        alert("Google login tidak bisa dibuka di dalam aplikasi. Silakan buka di browser seperti Safari atau Chrome.");
      });
    }
  });
</script>
@endsection
