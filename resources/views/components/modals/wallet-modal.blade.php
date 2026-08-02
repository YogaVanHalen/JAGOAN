<div id="walletModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative animate-in fade-in zoom-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    🏦
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">Tambah Rekening / Kartu Kredit</h3>
                    <p class="text-xs text-slate-400">Tambahkan akun bank, e-wallet, kartu kredit, atau paylater</p>
                </div>
            </div>
            <button onclick="closeModal('walletModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold p-1">✕</button>
        </div>

        <form action="{{ route('wallets.store') }}" method="POST" class="space-y-4 pt-4">
            @csrf

            <!-- Toggle Credit Card / Paylater -->
            <div class="bg-slate-50 dark:bg-slate-800/80 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <label class="font-bold text-xs text-slate-800 dark:text-slate-100 block">💳 Ini Adalah Kartu Kredit / Paylater / Pinjol</label>
                    <p class="text-[10px] text-slate-400">Transaksi via kartu ini akan otomatis menambah Pokok Hutang.</p>
                </div>
                <input type="checkbox" name="is_credit" id="is_credit_modal" value="1" onchange="toggleCreditFieldsModal(this.checked)" class="w-4 h-4 text-rose-600 rounded cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rekening / Akun *</label>
                <input type="text" name="name" required placeholder="Contoh: BCA Everyday Card, Gopay Later, Tabungan Utama" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Dompet *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                    <option value="personal">🔒 Dompet Pribadi (Hanya Anda)</option>
                    <option value="shared">👥 Dompet Bersama (Siap Di-invite)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bank / Penyedia Layanan *</label>
                    <select name="bank_name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                        @php
                            $bOptions = ['BCA', 'Mandiri', 'BRI', 'BNI', 'Bank Jago', 'Seabank', 'BSI', 'CIMB Niaga', 'Danamon', 'Permata', 'GoPay', 'OVO', 'ShopeePay', 'Dana', 'LinkAja', 'Kredivo', 'Akulaku', 'Tunai / Cash'];
                        @endphp
                        @foreach($bOptions as $bank)
                            <option value="{{ $bank }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rekening / Akun (Opsional)</label>
                    <input type="text" name="account_number" placeholder="Contoh: 1234567890" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Conditional Credit Card Fields -->
            <div id="creditFieldsModal" class="space-y-4 pt-2 border-t border-slate-100 dark:border-slate-800 hidden">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jaringan Kartu / Jenis Kredit</label>
                    <select name="card_network" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                        @php
                            $cNetworks = ['Visa', 'Mastercard', 'Amex', 'JCB', 'GPN', 'Paylater / Pinjol'];
                        @endphp
                        @foreach($cNetworks as $network)
                            <option value="{{ $network }}" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100">{{ $network }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Limit Kredit (Rp)</label>
                        <input type="text" name="credit_limit" placeholder="Contoh: 15.000.000" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Bunga Custom (% Per Bulan)</label>
                        <input type="number" step="0.01" name="interest_rate_percent" value="1.75" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <div class="flex items-center justify-between bg-rose-50/50 dark:bg-rose-950/30 p-2.5 rounded-xl border border-rose-200/50 dark:border-rose-800/40">
                    <div class="leading-tight">
                        <label for="auto_accrue_wallet" class="text-xs font-bold text-slate-800 dark:text-slate-200 block cursor-pointer">📈 Hitung & Akumulasikan Bunga Otomatis</label>
                        <span class="text-[10px] text-slate-400">Bunga dihitung otomatis H+1 setelah tanggal jatuh tempo.</span>
                    </div>
                    <input type="checkbox" name="auto_accrue_interest" id="auto_accrue_wallet" value="1" class="w-4 h-4 text-rose-600 rounded cursor-pointer">
                </div>
            </div>

            <div>
                <label id="balanceLabelModal" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Saldo Awal (Rp)</label>
                <input type="text" name="balance" placeholder="0" value="0" onkeyup="formatCurrencyInput(this)" onchange="formatCurrencyInput(this)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-3 flex gap-3">
                <button type="button" onclick="closeModal('walletModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold py-2.5 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCreditFieldsModal(checked) {
    const fields = document.getElementById('creditFieldsModal');
    const label = document.getElementById('balanceLabelModal');
    if (checked) {
        fields.classList.remove('hidden');
        label.innerText = 'Pokok Hutang Saat Ini / Kredit Terpakai (Rp)';
    } else {
        fields.classList.add('hidden');
        label.innerText = 'Saldo Awal (Rp)';
    }
}
</script>
