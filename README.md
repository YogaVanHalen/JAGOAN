# 💸 JAGOAN - Smart Cash Flow & Debt Tracker for Family

**JAGOAN** adalah aplikasi manajemen keuangan keluarga modern berbasis Laravel yang intuitif, ultra-responsif, dan kaya fitur. Dirancang khusus untuk membantu keluarga mengelola arus kas (pemasukan & pengeluaran), memantau saldo berbagai rekening/e-wallet, mengontrol kartu kredit & paylater, menata cicilan hutang, mengelola **Dompet Bersama & Sub-User**, hingga menggapai target finansial (*Goals*).

---

## ✨ Fitur Unggulan Utama

- 🔑 **Username Login System**: Login praktis menggunakan **Username** (atau Email), dengan dukungan pengaturan Username & Email terpisah pada halaman Profil.
- 👑 **3-Tier Hierarchy Role System**:
  - `superadmin`: Akses penuh manajemen sistem, pendaftaran admin/pengguna, dan kontrol server.
  - `admin` (Wallet Owner): Dapat membuat akun pengguna keluarga (*sub-user*), mengatur kata sandi, serta membagikan akses dompet (*shared wallet*).
  - `user`: Pengguna biasa atau anggota keluarga yang mengelola dompet pribadi atau dompet bersama yang diberikan akses oleh Admin.
- 👨‍👩‍👧‍👦 **Anggota & Share Wallet (Sub-Users)**: Wallet Owner dapat membuat akun anggota keluarga dibawahnya dengan kata sandi custom dan menentukan dompet mana saja yang dapat diakses oleh anggota tersebut.
- 💵 **Live Format Angka Otomatis (*Thousands Separator Masking*)**: Pengetikan nominal angka otomatis terformat dengan titik ribuan (*misal: pengetikan `1000000` otomatis menjadi `1.000.000`*) untuk mencegah kesalahan input.
- 📊 **Smart Financial Health Advisory Banner**: Banner arus kas interaktif dilengkapi indikator tingkat kesehatan keuangan *segaris (single-line)* dengan 4 tingkatan peringatan:
  - 🟢 **0-25%**: Sangat Baik
  - 🔵 **26-50%**: Mulai Waspada
  - ⚠️ **51-75%**: Perlu Kehati-hatian
  - 🚨 **>76%**: Peringatan Kritis + Informasi Sisa Saldo Belanja
- 📈 **Badge Perbandingan Tren Bulanan**: Kartu Pemasukan & Pengeluaran dilengkapi indikator persentase pertumbuhan dibanding bulan sebelumnya (*misal: `↑ +15.4% vs bln lalu`*).
- 📁 **Master Backup Excel Multi-Sheet (.xls)**: Fitur 1-klik unduh seluruh data keuangan ke dalam 1 file Master Excel dengan 6 Tab Sheet terpisah (`Pemasukan`, `Pengeluaran`, `Rekening & Dompet`, `Kategori`, `Hutang & Cicilan`, `Target Finansial`).
- 📥 **Master Import Data & Konfirmasi Pengunggahan**: 1 file template CSV terpadu untuk impor data massal dilengkapi modal konfirmasi jumlah data saat ini vs data yang akan diunggah.
- 🏦 **Multi Rekening & E-Wallet**: Kelola BCA, Mandiri, GoPay, OVO, ShopeePay, hingga Dompet Cash.
- 💳 **Kartu Kredit & Paylater Support**: Lacak limit kredit, suku bunga, tanggal jatuh tempo, dan akumulasi bunga otomatis.
- 📉 **Manajemen Hutang & Cicilan**: Pantau sisa pokok hutang, tenor bulanan, serta pelunasan instan.
- 🎯 **Target Finansial (Goals)**: Visualisasi progres pencapaian target tabungan atau pelunasan hutang.
- 🌙 **Dual-Mode Theme (Terang & Gelap)**: Pengalih tema instan dengan kontras warna tinggi yang nyaman di mata.

---

## 📖 Panduan Penggunaan Aplikasi (User Manual)

Halaman dokumentasi interaktif bawaan dapat diakses langsung melalui aplikasi pada menu **Setting (⚙️) -> Panduan & Dokumentasi** atau membuka URL `http://domain-anda.com/docs`.

### 📂 1. Menambahkan & Mengelola Kategori Transaksi
1. Buka menu **Setting (⚙️)** pada navigasi bawah, lalu pilih **Kelola Kategori**.
2. Klik **+ Tambah Kategori**.
3. Isi Nama Kategori (misal: *Gaji, Makanan, Transportasi, Listrik*).
4. Pilih Tipe: **Pemasukan** (hijau) atau **Pengeluaran** (merah).
5. Pilih ikon & warna label, lalu simpan.
   > *Sistem secara otomatis menyaring kategori sesuai jenis formulir transaksi yang sedang dibuka.*

### 💳 2. Menambahkan Rekening & Dompet
1. Buka menu **Rekening** pada navigasi bawah.
2. Klik **+ Tambah Rekening**.
3. Pilih Tipe Rekening:
   - **Bank / Tunai / E-Wallet:** Untuk rekening ber-saldo positif (BCA, Mandiri, Cash, GoPay).
   - **Kartu Kredit:** Untuk mencatat limit kredit, jatuh tempo, dan bunga bulanan.
4. Masukkan Saldo Awal dan simpan.

### 📥 3. Menambahkan Transaksi Pemasukan
1. Klik menu **Masuk** pada navigasi bawah.
2. Klik **+ Tambah Pemasukan**.
3. Ketik Nominal. *(Contoh: Pengetikan `1000000` akan otomatis terformat menjadi `1.000.000`)*.
4. Pilih Kategori Pemasukan dan Rekening Tujuan penerima dana.
5. Pilih Tanggal & beri Catatan (opsional), lalu klik Simpan.

### 📤 4. Menambahkan Transaksi Pengeluaran
1. Klik menu **Keluar** pada navigasi bawah.
2. Klik **+ Tambah Pengeluaran**.
3. Ketik Nominal (otomatis terformat titik ribuan).
4. Pilih Kategori Pengeluaran & Rekening Sumber Dana yang digunakan.
5. Klik Simpan. Saldo rekening sumber dana akan berkurang secara otomatis.

### 🤝 5. Mengelola Hutang & Piutang
1. Buka menu **Hutang** pada navigasi bawah.
2. Pilih Tipe: **Hutang Saya** (pinjaman yang wajib kita bayar) atau **Piutang** (uang kita yang dipinjam orang lain).
3. Masukkan total nominal, nama orang/instansi, dan tanggal jatuh tempo.
4. Klik tombol **Bayar** saat melakukan cicilan atau pelunasan.

### 🎯 6. Target Keuangan (Goals)
1. Klik menu **Goals** pada navigasi bawah -> **+ Buat Target Baru**.
2. Isi Nama Target (misal: *Dana Darurat, Tabungan Umroh*), target nominal, dan tanggal target.
3. Klik **Setor Dana** setiap kali menambah tabungan hingga indikator progress mencapai 100%.

### 👥 7. Mengundang & Mengelola Anggota Keluarga (Shared Wallet)
1. Buka menu **Setting (⚙️)** -> **Anggota & Share Wallet**.
2. Klik **+ Tambah Anggota Keluarga**.
3. Masukkan Username / Email anggota keluarga. Anggota yang ditambahkan akan dapat mencatat transaksi pada dompet bersama yang dibagikan.

### 📥 8. Backup & Import Data Excel (Dengan Persetujuan)
1. Buka menu **Setting (⚙️)** -> **Backup & Import Data**.
2. **Unduh Master Excel:** 1-klik unduh seluruh data Pemasukan, Pengeluaran, Rekening, Kategori, Hutang, & Target Finansial.
3. **Import Data:** Pilih file Excel transaksi -> Klik Upload -> Tinjau modal peringatan persetujuan jumlah data saat ini vs data baru -> Klik Setujui & Impor.

---

## 🌐 Panduan Lengkap Instalasi & Update di aaPanel

Aplikasi ini dapat diinstal langsung di **Root Domain** aaPanel tanpa subfolder.

### 📋 1. Persiapan PHP di aaPanel
1. Masuk ke **Dashboard aaPanel** -> **App Store**.
2. Buka **Setting** pada versi PHP yang digunakan (**PHP 8.1 / 8.2 / 8.3**).
3. Di tab **Install extensions**, pastikan terpasang:
   - `fileinfo`, `mbstring`, `openssl`, `curl`, `mysqli`, `pdo_mysql` / `pdo_sqlite`.

---

### 🌐 2. Tambah Situs di aaPanel
1. Buka menu **Website** -> **Add site**.
2. **Domain name**: `fh.belivoucher.my.id` (sesuaikan dengan domain Anda).
3. **Site directory**: `/www/wwwroot/fh.belivoucher.my.id`.
4. **PHP version**: Pilih **PHP-8.2** atau **PHP-8.1**.

---

### 📥 3. Clone Kode dari GitHub
Buka **Terminal SSH** di aaPanel, masuk ke folder situs Anda, hapus file bawaan aaPanel (`index.html`, `.user.ini`), lalu jalankan clone:

```bash
cd /www/wwwroot/fh.belivoucher.my.id
chattr -i .user.ini 2>/dev/null
rm -rf index.html 404.html .user.ini .htaccess default
git clone https://github.com/YogaVanHalen/JAGOAN.git .
```

> 💡 **Metode Alternatif (Jika `git clone .` gagal / Folder Sudah Terisi):**
> Anda juga bisa menggunakan `git init` berikut (tidak akan error meskipun folder ada isinya):
> ```bash
> cd /www/wwwroot/fh.belivoucher.my.id
> chattr -i .user.ini 2>/dev/null
> git init
> git remote add origin https://github.com/YogaVanHalen/JAGOAN.git 2>/dev/null || git remote set-url origin https://github.com/YogaVanHalen/JAGOAN.git
> git fetch origin main
> git checkout -B main origin/main -f
> ```

---

### ⚡ 4. Jalankan Skrip Instalasi Otomatis
Di Terminal SSH aaPanel, jalankan:

```bash
bash install-aapanel.sh admin@email.com
```

Skrip ini akan secara otomatis mendeteksi jika instalasi dilakukan dari subfolder (misal `git clone` tanpa titik), memindahkan seluruh file ke Root Domain jika diperlukan, menyiapkan `.env`, memasang dependensi composer, menjalankan migrasi database, membuat akun Admin, serta mengonfigurasi izin folder `storage` & `bootstrap/cache`.

---

### ⚙️ 5. Konfigurasi Running Directory & Nginx Rewrite
1. Di aaPanel -> **Website** -> Klik domain Anda.
2. Tab **Site directory**:
   - Set **Running directory** ke **/public** (*Sangat Penting!*).
   - Hilangkan centang **Anti-user-site / open_basedir**.
3. Tab **URL rewrite**:
   - Pilih preset **laravel** (atau paste `try_files $uri $uri/ /index.php?$query_string;`).

---

### 🔄 Perintah Update Otomatis (Saat ada Fitur Baru)

Untuk memperbarui aplikasi tanpa menghapus data yang ada, jalankan perintah ini di Terminal SSH aaPanel:

```bash
cd /www/wwwroot/fh.belivoucher.my.id
bash update-aapanel.sh
```

---

## 🔑 Akun Login Default

- **Username**: `admin` (atau Email: `admin@email.com`)
- **Password**: `123456`

---

## 🛡️ Fitur Keamanan Cloudflare Turnstile (Opsional)

Aplikasi **JAGOAN** telah mendukung verifikasi keamanan **Cloudflare Turnstile** pada halaman Login. Jika Anda ingin mengaktifkannya:
1. Buat **Turnstile Widget** di [Dashboard Cloudflare](https://dash.cloudflare.com/).
2. Tambahkan `TURNSTILE_SITE_KEY` dan `TURNSTILE_SECRET_KEY` pada file `.env`:

```env
TURNSTILE_SITE_KEY=0x4AAAAAA...
TURNSTILE_SECRET_KEY=0x4AAAAAA...
```

*Jika kunci tidak diisi di `.env`, sistem akan secara otomatis melewati verifikasi tanpa mengganggu alur login biasa.*

---

## 🛠️ Instalasi Lokal (Development)

```bash
# 1. Clone repositori
git clone https://github.com/YogaVanHalen/JAGOAN.git
cd JAGOAN

# 2. Install dependensi
composer install
npm install

# 3. Setup .env & Application Key
cp .env.example .env
php artisan key:generate

# 4. Migrasi Database & Seeding
php artisan migrate:fresh --seed

# 5. Jalankan Server Lokal
php artisan serve
```

Akses aplikasi di browser: **`http://127.0.0.1:8000`**

---

## 📄 Lisensi

Project ini dirilis di bawah lisensi [MIT License](LICENSE).
