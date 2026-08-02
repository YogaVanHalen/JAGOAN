@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 p-6 sm:p-10 text-white shadow-xl">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider">
                    <span>📖 Panduan Lengkap</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Dokumentasi & Cara Penggunaan JAGOAN</h1>
                <p class="text-indigo-100 text-sm sm:text-base max-w-2xl">
                    Panduan langkah demi langkah mengelola arus kas keluarga, pencatatan pemasukan & pengeluaran, rekening dompet, hutang piutang, hingga import/export data Excel.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-700 font-bold rounded-xl text-xs hover:bg-indigo-50 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Navigation / Daftar Isi -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span>📌</span> Daftar Isi Panduan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-xs font-semibold">
                <a href="#step-categories" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">1</span>
                    Kelola Kategori Transaksi
                </a>
                <a href="#step-wallets" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">2</span>
                    Kelola Rekening & Dompet
                </a>
                <a href="#step-income" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">3</span>
                    Input Pemasukan
                </a>
                <a href="#step-expense" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-slate-700 dark:text-slate-200 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">4</span>
                    Input Pengeluaran
                </a>
                <a href="#step-debts" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-slate-700 dark:text-slate-200 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">5</span>
                    Hutang & Piutang
                </a>
                <a href="#step-goals" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">6</span>
                    Target Keuangan (Goals)
                </a>
                <a href="#step-analytics" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">7</span>
                    Banner Analisis & Status
                </a>
                <a href="#step-family" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">8</span>
                    Anggota Keluarga
                </a>
                <a href="#step-excel" class="p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200/60 dark:border-slate-700 transition flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">9</span>
                    Import & Export Excel
                </a>
            </div>
        </div>

        <!-- Section List -->
        <div class="space-y-8">
            
            <!-- 1. Kategori -->
            <section id="step-categories" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">1</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Menambahkan & Mengelola Kategori Transaksi</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur kategori pemasukan dan pengeluaran sesuai kebutuhan keuangan Anda.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <p>Sebelum mencatat transaksi, disarankan untuk menyiapkan kategori transaksi terlebih dahulu agar pencatatan arus kas terkelola dengan rapi.</p>
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Buka menu <span class="font-bold text-slate-900 dark:text-slate-100">Setting (⚙️)</span> pada navigasi bawah, lalu pilih <span class="font-bold text-slate-900 dark:text-slate-100">Kelola Kategori</span>.</li>
                        <li>Klik tombol <span class="font-bold text-indigo-600 dark:text-indigo-400">+ Tambah Kategori</span>.</li>
                        <li>Isi **Nama Kategori** (misal: *Gaji, Makanan, Listrik, Transportasi*).</li>
                        <li>Pilih **Tipe Kategori**: <span class="px-2 py-0.5 rounded text-xs bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 font-bold">Pemasukan</span> atau <span class="px-2 py-0.5 rounded text-xs bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300 font-bold">Pengeluaran</span>.</li>
                        <li>Pilih warna label dan ikon yang sesuai, lalu klik <span class="font-bold text-slate-900 dark:text-slate-100">Simpan</span>.</li>
                    </ol>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2">
                        <span>💡</span>
                        <span><strong>Tips:</strong> Aplikasi secara otomatis menyaring kategori sesuai jenis transaksi yang sedang dipilih (Pemasukan hanya menampilkan kategori Pemasukan, Pengeluaran hanya menampilkan kategori Pengeluaran).</span>
                    </div>
                </div>
            </section>

            <!-- 2. Rekening & Dompet -->
            <section id="step-wallets" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">2</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Menambahkan Rekening, Bank & Kartu Kredit</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pusat pengelolaan saldo Tunai, Bank, E-Wallet, dan Tagihan Kartu Kredit.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Buka menu <span class="font-bold text-slate-900 dark:text-slate-100">Rekening</span> pada navigasi bawah.</li>
                        <li>Klik tombol <span class="font-bold text-indigo-600 dark:text-indigo-400">+ Tambah Rekening</span>.</li>
                        <li>Pilih **Tipe Rekening**:
                            <ul class="list-disc list-inside ml-6 mt-1 space-y-1 text-xs">
                                <li><strong class="text-slate-800 dark:text-slate-200">Bank / Tunai / E-Wallet:</strong> Untuk rekening saldo aktif (BCA, Mandiri, Cash, GoPay).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Kartu Kredit:</strong> Untuk mencatat limit kredit, tanggal jatuh tempo, dan bunga bulanan.</li>
                            </ul>
                        </li>
                        <li>Isi Saldo Awal, Nama Rekening, dan Simpan.</li>
                    </ol>
                </div>
            </section>

            <!-- 3. Input Pemasukan -->
            <section id="step-income" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg">3</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Menginput Transaksi Pemasukan</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Catat pendapatan gaji, bonus, hasil usaha, atau transfer masuk.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Klik menu <span class="font-bold text-emerald-600 dark:text-emerald-400">Masuk</span> pada navigasi bawah.</li>
                        <li>Klik tombol <span class="font-bold text-emerald-600 dark:text-emerald-400">+ Tambah Pemasukan</span>.</li>
                        <li>Ketik Nominal. <span class="text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 px-2 py-0.5 rounded font-bold">Fitur Auto Format:</span> Saat diketik <code class="bg-slate-100 dark:bg-slate-700 px-1 py-0.5 rounded">1000000</code> akan otomatis terformat menjadi <code class="bg-slate-100 dark:bg-slate-700 px-1 py-0.5 rounded">1.000.000</code>.</li>
                        <li>Pilih Kategori Pemasukan dan Rekening Tujuan penerima uang.</li>
                        <li>Pilih Tanggal dan beri Catatan (opsional), lalu simpan.</li>
                    </ol>
                </div>
            </section>

            <!-- 4. Input Pengeluaran -->
            <section id="step-expense" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg">4</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Menginput Transaksi Pengeluaran</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Catat semua biaya harian, belanja, cicilan, dan tagihan bulanan.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Klik menu <span class="font-bold text-rose-600 dark:text-rose-400">Keluar</span> pada navigasi bawah.</li>
                        <li>Klik tombol <span class="font-bold text-rose-600 dark:text-rose-400">+ Tambah Pengeluaran</span>.</li>
                        <li>Masukkan Nominal (terformat otomatis menjadi titik ribuan).</li>
                        <li>Pilih Kategori Pengeluaran & Rekening Sumber Dana yang digunakan untuk membayar.</li>
                        <li>Simpan transaksi. Saldo rekening sumber dana akan berkurang secara otomatis.</li>
                    </ol>
                </div>
            </section>

            <!-- 5. Hutang & Piutang -->
            <section id="step-debts" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg">5</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Mengelola Hutang & Piutang</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pantau pinjaman ke orang lain atau kewajiban membayar hutang.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Buka menu <span class="font-bold text-slate-900 dark:text-slate-100">Hutang</span> pada navigasi bawah.</li>
                        <li>Pilih tipe: <span class="font-bold text-rose-600">Hutang Saya</span> (uang yang kita pinjam) atau <span class="font-bold text-emerald-600">Piutang</span> (uang yang dipinjam orang lain dari kita).</li>
                        <li>Masukkan total nominal, nama pemberi/peminjam, dan tanggal jatuh tempo.</li>
                        <li>Setiap ada pembayaraan cicilan/pelunasan, klik tombol **Bayar** pada daftar hutang tersebut.</li>
                    </ol>
                </div>
            </section>

            <!-- 6. Target Keuangan (Goals) -->
            <section id="step-goals" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">6</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Menetapkan Target Keuangan (Goals)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Wujudkan impian dana darurat, pembelian barang, atau liburan keluarga.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Klik menu <span class="font-bold text-indigo-600 dark:text-indigo-400">Goals</span> pada navigasi bawah.</li>
                        <li>Klik <span class="font-bold text-indigo-600 dark:text-indigo-400">+ Buat Target Baru</span>.</li>
                        <li>Isi Nama Target (misal: *Dana Darurat 6 Bulan, Beli Laptop*), target nominal, dan target tanggal tercapai.</li>
                        <li>Kapan pun Anda menambah tabungan untuk target ini, cukup klik **Setor Dana**. Progress bar akan meningkat otomatis hingga 100%.</li>
                    </ol>
                </div>
            </section>

            <!-- 7. Banner Analisis -->
            <section id="step-analytics" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">7</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Memahami Banner Analisis Arus Kas & Warning</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Fitur kesehatan keuangan otomatis pada halaman Beranda (Dashboard).</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <p>Di bagian atas Beranda, terdapat Banner Pintar yang menampilkan:</p>
                    <ul class="list-disc list-inside space-y-2 text-xs font-semibold">
                        <li><span class="text-slate-900 dark:text-slate-100">Nama Pengguna & Jam Real-Time</span> sesuai zona waktu perangkat Anda.</li>
                        <li><span class="text-slate-900 dark:text-slate-100">Perbandingan Bulan Lalu:</span> Persentase kenaikan/penurunan Pemasukan dan Pengeluaran dibandingkan bulan sebelumnya.</li>
                        <li><span class="text-slate-900 dark:text-slate-100">Level Peringatan Pengeluaran (Progress Bar):</span>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-normal">
                                <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/40 rounded-lg">
                                    <strong class="text-emerald-700 dark:text-emerald-300">0% - 25%:</strong> Sangat baik & sehat mengelola keuangan.
                                </div>
                                <div class="p-2 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/40 rounded-lg">
                                    <strong class="text-blue-700 dark:text-blue-300">26% - 50%:</strong> Pengeluaran wajar, mulai waspada.
                                </div>
                                <div class="p-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/40 rounded-lg">
                                    <strong class="text-amber-700 dark:text-amber-300">51% - 75%:</strong> Perlu kehati-hatian dalam belanja.
                                </div>
                                <div class="p-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/40 rounded-lg">
                                    <strong class="text-rose-700 dark:text-rose-300">&gt; 76% (WARNING):</strong> Peringatan bahaya! Pengeluaran hampir menghabiskan pemasukan & menampilkan sisa kemampuan saldo.
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- 8. Anggota Keluarga -->
            <section id="step-family" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">8</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Mengundang & Mengelola Anggota Keluarga</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dompet dan transaksi bersama pasangan atau anggota keluarga.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Buka menu <span class="font-bold text-slate-900 dark:text-slate-100">Setting (⚙️)</span> -> <span class="font-bold text-indigo-600 dark:text-indigo-400">Kelola Anggota</span>.</li>
                        <li>Klik <span class="font-bold text-indigo-600 dark:text-indigo-400">+ Tambah Anggota Keluarga</span>.</li>
                        <li>Masukkan Username / Email pasangan atau anggota keluarga Anda.</li>
                        <li>Anggota yang ditambahkan akan dapat mencatat transaksi pada dompet bersama yang telah dibagikan.</li>
                    </ol>
                </div>
            </section>

            <!-- 9. Import & Export -->
            <section id="step-excel" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg">9</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">Import & Export Data Excel (Gabungan & Persetujuan)</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Backup data keuangan ke format Excel atau upload data massal.</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <ol class="list-decimal list-inside space-y-2 font-medium">
                        <li>Buka menu <span class="font-bold text-slate-900 dark:text-slate-100">Setting (⚙️)</span> -> <span class="font-bold text-indigo-600 dark:text-indigo-400">Backup & Export Data</span>.</li>
                        <li><strong>Unduh Template Excel:</strong> Klik tombol <span class="font-bold text-emerald-600 dark:text-emerald-400">Unduh Template Excel (Gabungan)</span>. Template ini menyatukan format Pemasukan & Pengeluaran dalam 1 file yang mudah diisi.</li>
                        <li><strong>Upload / Import Data:</strong> Pilih file Excel transaksi Anda, lalu klik Upload.</li>
                        <li><strong>Peringatan Persetujuan:</strong> Sistem akan menampilkan modal konfirmasi yang menginfokan <span class="font-bold text-indigo-600 dark:text-indigo-400">jumlah data yang ada di database saat ini</span> serta <span class="font-bold text-indigo-600 dark:text-indigo-400">jumlah data baru yang akan di-upload</span> sebelum data diproses.</li>
                    </ol>
                </div>
            </section>

        </div>

        <!-- Footer Callout -->
        <div class="text-center py-6 border-t border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Punya pertanyaan lain atau butuh bantuan lebih lanjut? Buka menu <a href="{{ route('settings.index') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Pengaturan</a> atau hubungi pengelola aplikasi.
            </p>
        </div>

    </div>
</div>
@endsection
