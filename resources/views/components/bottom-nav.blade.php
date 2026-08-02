<div class="fixed bottom-0 left-0 right-0 z-50 flex justify-center px-2 sm:px-4 pb-2.5 pt-1 pointer-events-none">
    <div class="pointer-events-auto w-full max-w-lg bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 shadow-2xl rounded-2xl px-1 sm:px-3 py-1.5 sm:py-2 grid grid-cols-7 items-center transition-colors duration-300">
        
        <!-- 1. Beranda -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Beranda</span>
        </a>

        <!-- 2. Pemasukan -->
        <a href="{{ route('income.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('income.*') ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-6-6m6 6l6-6" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Masuk</span>
        </a>

        <!-- 3. Pengeluaran -->
        <a href="{{ route('expense.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('expense.*') ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V4m0 0l-6 6m6-6l6 6" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Keluar</span>
        </a>

        <!-- 4. Rekening -->
        <a href="{{ route('wallets.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('wallets.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Rekening</span>
        </a>

        <!-- 5. Hutang -->
        <a href="{{ route('debts.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('debts.*') ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Hutang</span>
        </a>

        <!-- 6. Goals -->
        <a href="{{ route('goals.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('goals.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Goals</span>
        </a>

        <!-- 7. Setting -->
        <a href="{{ route('settings.index') }}" class="flex flex-col items-center justify-center gap-0.5 group py-1 px-0.5 rounded-xl transition-all {{ request()->routeIs('settings.*') || request()->routeIs('family.*') || request()->routeIs('categories.*') || request()->routeIs('export.*') || request()->routeIs('profile.*') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-[9px] sm:text-[10px] tracking-tight leading-none truncate w-full text-center">Setting</span>
        </a>
    </div>
</div>
