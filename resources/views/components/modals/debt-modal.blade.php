<div id="debtModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                    📉
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Catat Hutang / Pinjaman Baru</h3>
                    <p class="text-xs text-slate-400">Tambahkan catatan KPR, KTA, KKB, atau Pinjaman lainnya</p>
                </div>
            </div>
            <button onclick="closeModal('debtModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form action="{{ route('debts.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pinjaman / Hutang *</label>
                <input type="text" name="name" required placeholder="Contoh: KPR Rumah BSD, Cicilan Mobil Honda" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Pinjaman *</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                        <option value="KPR" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">KPR (Rumah/Apartemen)</option>
                        <option value="KKB" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">KKB (Kendaraan Bermotor)</option>
                        <option value="KTA" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">KTA (Kredit Tanpa Agunan)</option>
                        <option value="Kartu Kredit" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Kartu Kredit</option>
                        <option value="Paylater / Pinjol" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Paylater / Pinjol</option>
                        <option value="Lainnya" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Terkait *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                        <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">-- Pilih Kategori --</option>
                        @foreach($categories->where('type', 'expense') as $cat)
                            <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pokok Hutang Awal (Rp)</label>
                    <input type="text" name="initial_amount" placeholder="Contoh: 150.000.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Hutang Saat Ini (Rp) *</label>
                    <input type="text" name="remaining_amount" required placeholder="Contoh: 120.000.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cicilan / Bln (Rp)</label>
                    <input type="text" name="monthly_installment" placeholder="Contoh: 3.500.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Tenor (Bln)</label>
                    <input type="number" name="tenor_months" value="60" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sisa Tenor (Bln)</label>
                    <input type="number" name="remaining_tenor_months" value="48" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo (1 - 31)</label>
                    <input type="number" name="due_day" min="1" max="31" value="10" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Bunga Custom (% / Bln)</label>
                    <input type="number" step="0.01" name="interest_rate_percent" value="0" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="flex items-center justify-between bg-rose-50/50 dark:bg-rose-950/30 p-2.5 rounded-xl border border-rose-200/50 dark:border-rose-800/40">
                <div class="leading-tight">
                    <label for="auto_accrue_debt" class="text-xs font-bold text-slate-800 dark:text-slate-200 block cursor-pointer">📈 Hitung & Akumulasikan Bunga Otomatis</label>
                    <span class="text-[10px] text-slate-400">Bunga dihitung otomatis H+1 setelah tanggal jatuh tempo.</span>
                </div>
                <input type="checkbox" name="auto_accrue_interest" id="auto_accrue_debt" value="1" class="w-4 h-4 text-rose-600 rounded cursor-pointer">
            </div>

            <div class="flex items-center justify-between bg-indigo-50/50 dark:bg-indigo-950/30 p-2.5 rounded-xl border border-indigo-200/50 dark:border-indigo-800/40">
                <div class="leading-tight">
                    <label for="make_goal_debt" class="text-xs font-bold text-slate-800 dark:text-slate-200 block cursor-pointer">🎯 Jadikan Target Pelunasan (Goal)</label>
                    <span class="text-[10px] text-slate-400">Progres goal akan otomatis mengikuti sisa cicilan hutang ini.</span>
                </div>
                <input type="checkbox" name="make_goal" id="make_goal_debt" value="1" checked class="w-4 h-4 text-indigo-600 rounded cursor-pointer">
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('debtModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Catatan Hutang
                </button>
            </div>
        </form>
    </div>
</div>
