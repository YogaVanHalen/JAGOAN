<header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-30 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
        <!-- Brand & Current Page / Account Info -->
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-extrabold text-lg shadow-sm shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                    J
                </div>
                <div>
                    <h1 class="font-black text-slate-800 dark:text-slate-100 text-base tracking-tight leading-none">
                        JAGOAN <span class="text-indigo-500">.</span>
                    </h1>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-medium tracking-wide uppercase">Smart Cash Flow & Debt Tracker for Family</span>
                </div>
            </a>
        </div>

        <!-- Right Side: Account Indicator & Theme Toggle & Profile -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Active User Account Badge -->
            <div class="hidden sm:flex items-center gap-2 bg-slate-100 dark:bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-200/60 dark:border-slate-700/60">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <div class="text-left leading-tight">
                    <span class="block text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[140px] truncate">
                        {{ Auth::user()->name ?? 'Pengguna' }}
                    </span>
                    <span class="block text-[10px] text-slate-400 max-w-[140px] truncate">
                        {{ Auth::user()->email ?? '' }}
                    </span>
                </div>
            </div>

            <!-- Theme Switcher Button -->
            <button onclick="toggleTheme()" type="button" aria-label="Toggle Theme" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition border border-slate-200/60 dark:border-slate-700/60">
                <svg id="sunIconHeader" class="w-4 h-4 hidden text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="moonIconHeader" class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- User Menu Avatar Dropdown -->
            <div class="relative">
                <button id="avatarButtonHeader" type="button" class="flex items-center gap-1.5 focus:outline-none">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                </button>

                <div id="avatarMenuHeader" class="hidden absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 text-xs">
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 sm:hidden">
                        <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ Auth::user()->name ?? '' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    @if(Auth::user() && Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 font-bold transition border-b border-slate-100 dark:border-slate-800">
                            <span>👑</span> Admin Panel
                        </a>
                    @endif
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold transition border-b border-slate-100 dark:border-slate-800">
                        <span>⚙️</span> Pengaturan / Setting
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold transition border-b border-slate-100 dark:border-slate-800">
                        <span>👤</span> Edit Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-semibold transition flex items-center gap-2">
                            <span>🚪</span> Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarBtn = document.getElementById('avatarButtonHeader');
    const avatarMenu = document.getElementById('avatarMenuHeader');

    if (avatarBtn && avatarMenu) {
        avatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            avatarMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!avatarMenu.contains(e.target) && !avatarBtn.contains(e.target)) {
                avatarMenu.classList.add('hidden');
            }
        });
    }

    function syncThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const sun = document.getElementById('sunIconHeader');
        const moon = document.getElementById('moonIconHeader');
        if (sun && moon) {
            if (isDark) {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            } else {
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
            }
        }
    }
    syncThemeIcons();
    window.syncHeaderThemeIcons = syncThemeIcons;
});
</script>
