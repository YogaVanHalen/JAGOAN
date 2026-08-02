@extends('layouts.app')
@include('components.bottom-nav')

@section('content')
<div class="px-4 sm:px-6 py-6 transition-colors duration-300 pb-24 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">📥 Backup & Import Data (Excel)</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Unduh cadangan data lengkap atau impor transaksi baru dari file Excel dengan mudah</p>
        </div>
        <a href="{{ route('export.full') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-md transition flex items-center justify-center gap-2">
            <span>📥</span> Download Full Backup Excel (.xls)
        </a>
    </div>

    <!-- Security Banner -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 p-6 rounded-3xl shadow-lg text-white">
        <h2 class="text-lg font-bold flex items-center gap-2">
            <span>🔒</span> Proteksi Keamanan Data Pengguna
        </h2>
        <p class="text-xs sm:text-sm opacity-90 mt-1 leading-relaxed">
            Seluruh data Anda terpusat dan aman. File cadangan (*backup*) menggunakan format **Excel Multi-Sheet (.xls)** yang terbagi otomatis ke dalam tab sheet (Pemasukan, Pengeluaran, Rekening, Kategori, Hutang, dan Target).
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Unduh Backup Excel Multi-Sheet -->
        <div class="bg-white dark:bg-slate-900 border-2 border-emerald-500/30 dark:border-emerald-500/20 p-6 rounded-3xl shadow-sm flex flex-col justify-between space-y-5">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 font-extrabold flex items-center justify-center text-xl">
                    📊
                </div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">1. Unduh Full Backup Excel</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Mengunduh seluruh laporan keuangan Anda ke dalam **1 file Excel (.xls)**. Data terbagi rapi ke dalam **Multi-Tab Sheet** (Pemasukan, Pengeluaran, Rekening, Kategori, Hutang, &amp; Goals).
                </p>

                <div class="pt-2 text-xs text-slate-500 space-y-1">
                    <p>✓ Tab Sheet 1: 📈 Pemasukan ({{ $totalIncomes }} data)</p>
                    <p>✓ Tab Sheet 2: 📉 Pengeluaran ({{ $totalExpenses }} data)</p>
                    <p>✓ Tab Sheet 3: 🏦 Rekening &amp; Saldo ({{ $totalWallets }} dompet)</p>
                    <p>✓ Tab Sheet 4: 🏷️ Kategori ({{ $totalCategories }} kategori)</p>
                </div>
            </div>

            <a href="{{ route('export.full') }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-2xl text-xs font-bold text-center transition shadow-md flex items-center justify-center gap-2">
                <span>📥</span> Unduh Full Backup Excel (.xls)
            </a>
        </div>

        <!-- Card 2: Unduh Templat Excel Master (1 Templat Terpadu) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm flex flex-col justify-between space-y-5">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 font-extrabold flex items-center justify-center text-xl">
                    📋
                </div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg">2. Unduh Templat Excel (Input Offline)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Unduh 1 file templat Excel terpadu berkolom rapi untuk mengentri Pemasukan &amp; Pengeluaran sekaligus secara *offline* sebelum diunggah ke aplikasi.
                </p>

                <a href="{{ route('export.template') }}" class="w-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border border-indigo-200/60 hover:bg-indigo-100 py-3 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm text-center">
                    <span>📋</span> Download Templat Excel Master (.csv)
                </a>
            </div>

            <p class="text-[11px] text-slate-400 italic">
                💡 Isi kolom Jenis Transaksi (Pemasukan/Pengeluaran), Tanggal, Judul, Jumlah, Kategori, dan Rekening pada templat lalu unggah di form bawah.
            </p>
        </div>
    </div>

    <!-- Section 3: Upload / Impor Data dari Excel -->
    <div class="bg-white dark:bg-slate-900 border-2 border-indigo-500/30 dark:border-indigo-500/20 p-6 rounded-3xl shadow-sm space-y-4">
        <div class="pb-3 border-b border-slate-100 dark:border-slate-800">
            <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>📤</span> 3. Unggah / Impor File Excel ke Aplikasi
            </h2>
            <p class="text-xs text-slate-400">Unggah file Excel/CSV yang sudah Anda isi untuk memasukkan data transaksi sekaligus</p>
        </div>

        <form id="importForm" method="POST" action="{{ route('export.import') }}" enctype="multipart/form-data" onsubmit="handleImportSubmit(event)" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih File Excel / CSV yang Ingin Diunggah *</label>
                <input type="file" id="excelFile" name="file" accept=".csv,.txt,.xlsx,.xls" required onchange="inspectFile(event)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-950 dark:file:text-indigo-400 hover:file:bg-indigo-100">
            </div>

            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                    <span>📤</span> Unggah &amp; Impor Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi & Persetujuan Impor Data -->
<div id="confirmImportModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-3xl shadow-2xl p-6 relative space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base flex items-center gap-2">
                <span>⚠️</span> Konfirmasi Persetujuan Impor Data
            </h3>
            <button onclick="closeModal('confirmImportModal')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <div class="space-y-4 text-xs text-slate-600 dark:text-slate-300">
            <!-- Informasi Data Saat ini vs Data Diunggah -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 dark:bg-slate-800/80 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                    <span class="text-[11px] font-semibold text-slate-400 block">📊 Data Tersimpan Saat Ini</span>
                    <span class="text-base font-extrabold text-slate-800 dark:text-slate-100 mt-1 block">
                        {{ $totalIncomes + $totalExpenses }} Transaksi
                    </span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">({{ $totalIncomes }} Masuk, {{ $totalExpenses }} Keluar)</span>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-950/60 p-3.5 rounded-2xl border border-indigo-200/60 dark:border-indigo-800/60">
                    <span class="text-[11px] font-semibold text-indigo-500 dark:text-indigo-400 block">📁 Akan Di-upload</span>
                    <span id="modalRowCount" class="text-base font-extrabold text-indigo-600 dark:text-indigo-300 mt-1 block">
                        - Transaksi
                    </span>
                    <span id="modalFileName" class="text-[10px] text-indigo-500 dark:text-indigo-400 block mt-0.5 truncate">
                        file.csv
                    </span>
                </div>
            </div>

            <!-- Warning Notice -->
            <div class="bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 p-4 rounded-2xl text-amber-800 dark:text-amber-200 space-y-1.5">
                <h4 class="font-bold text-xs flex items-center gap-1.5">
                    <span>💡</span> Penting untuk Diketahui:
                </h4>
                <ul class="list-disc list-inside space-y-1 text-[11px] opacity-90 leading-relaxed">
                    <li>Data baru dari file Excel ini akan **ditambahkan** ke dalam akun Anda tanpa menghapus data lama.</li>
                    <li>Jika nama Kategori atau Rekening pada file belum ada, sistem akan **otomatis membuatkannya** untuk Anda.</li>
                </ul>
            </div>

            <!-- Consent Checkbox -->
            <label class="flex items-start gap-2.5 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl cursor-pointer border border-slate-200/80 dark:border-slate-700">
                <input type="checkbox" id="consentCheckbox" class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                    Saya menyetujui untuk memasukkan seluruh data transaksi dari file ini ke dalam akun JAGOAN saya.
                </span>
            </label>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="button" onclick="closeModal('confirmImportModal')" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl text-xs">
                ❌ Batal
            </button>
            <button type="button" onclick="proceedImport()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-2xl text-xs shadow-md transition">
                ✅ Ya, Setuju &amp; Impor Sekarang
            </button>
        </div>
    </div>
</div>

<script>
let detectedRows = 0;
let selectedFileName = '';

function inspectFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    selectedFileName = file.name;
    const reader = new FileReader();

    reader.onload = function(e) {
        const text = e.target.result;
        const lines = text.split('\n').filter(line => line.trim().length > 0 && !line.startsWith('sep=') && !line.includes('==='));
        
        // Exclude header row if present
        detectedRows = Math.max(0, lines.length - 1);
    };

    reader.readAsText(file);
}

function handleImportSubmit(event) {
    event.preventDefault();
    
    const fileInput = document.getElementById('excelFile');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Pilih file Excel / CSV terlebih dahulu.');
        return;
    }

    document.getElementById('modalFileName').textContent = selectedFileName || fileInput.files[0].name;
    document.getElementById('modalRowCount').textContent = detectedRows > 0 ? `± ${detectedRows} Transaksi` : 'Beberapa Transaksi';
    document.getElementById('consentCheckbox').checked = false;

    openModal('confirmImportModal');
}

function proceedImport() {
    const consent = document.getElementById('consentCheckbox');
    if (!consent.checked) {
        alert('Silakan centang kotak persetujuan terlebih dahulu sebelum mengimpor data.');
        return;
    }

    closeModal('confirmImportModal');
    document.getElementById('importForm').submit();
}
</script>
@endsection
