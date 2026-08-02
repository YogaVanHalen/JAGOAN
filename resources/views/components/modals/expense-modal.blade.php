<div id="expenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                    ➖
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Catat Pengeluaran Baru</h3>
                    <p class="text-xs text-slate-400">Catat transaksi belanja atau pembayaran cicilan</p>
                </div>
            </div>
            <button onclick="closeModal('expenseModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form action="{{ route('expense.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Keperluan *</label>
                <input type="text" name="title" required placeholder="Contoh: Belanja Bulanan, Bayar KPR, Makan Siang" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Pengeluaran (Rp) *</label>
                    <input type="text" name="amount" required placeholder="Contoh: 150.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sumber Pembayaran (Rekening/Kartu)</label>
                    <select name="wallet_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                        <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Pilih Sumber Uang</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                {{ $w->name }} {{ $w->is_credit ? '[💳 Kredit - Menambah Hutang]' : '[💵 Cash/Tabungan]' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Untuk Bayar Cicilan / Hutang</label>
                    <select name="debt_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                        <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">Bukan Pembayaran Cicilan</option>
                        @foreach($debts as $debt)
                            <option value="{{ $debt->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">
                                {{ $debt->name }} (Sisa: Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Pengeluaran *</label>
                <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">-- Pilih Kategori --</option>
                    @foreach($categories->where('type', 'expense') as $cat)
                        <option value="{{ $cat->id }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('expenseModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>
