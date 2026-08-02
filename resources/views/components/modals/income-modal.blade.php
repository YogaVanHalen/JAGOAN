<div id="incomeModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    ➕
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Catat Pemasukan Baru</h3>
                    <p class="text-xs text-slate-400">Tambahkan sumber penghasilan ke dompet Anda</p>
                </div>
            </div>
            <button onclick="closeModal('incomeModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form action="{{ route('income.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Sumber Pemasukan *</label>
                <input type="text" name="title" required placeholder="Contoh: Gaji Bulanan, Bonus Project" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Pemasukan (Rp) *</label>
                    <input type="text" name="amount" required placeholder="Contoh: 10.000.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pemasukan *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                        <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">-- Pilih Kategori --</option>
                        @foreach($categories->where('type', 'income') as $cat)
                            <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masuk ke Rekening / E-Wallet</label>
                    <select name="wallet_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                        <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Rekening Tujuan</option>
                        @foreach($wallets->where('is_credit', false) as $wallet)
                            <option value="{{ $wallet->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $wallet->name }} ({{ $wallet->bank_name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('incomeModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Pemasukan
                </button>
            </div>
        </form>
    </div>
</div>
