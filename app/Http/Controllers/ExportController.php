<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Wallet;
use App\Models\Debt;
use App\Models\Goal;
use App\Models\Category;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $totalIncomes = Income::where('user_id', $userId)->count();
        $totalExpenses = Expense::where('user_id', $userId)->count();
        $totalWallets = Wallet::getWalletsForUser($userId)->count();
        $totalCategories = Category::where('user_id', $userId)->count();
        $totalDebts = Debt::where('user_id', $userId)->count();
        $totalGoals = Goal::where('user_id', $userId)->count();

        return view('export.index', compact(
            'totalIncomes',
            'totalExpenses',
            'totalWallets',
            'totalCategories',
            'totalDebts',
            'totalGoals'
        ));
    }

    /**
     * Master Full Backup - Generates a Multi-Sheet Excel Workbook (.xls)
     * containing separate worksheets for Pemasukan, Pengeluaran, Rekening, Kategori, Hutang, Goals.
     */
    public function exportFullBackup()
    {
        $userId = Auth::id();
        $categories = Category::where('user_id', $userId)->get();
        $incomes = Income::where('user_id', $userId)->with(['category', 'wallet'])->latest('date')->get();
        $expenses = Expense::where('user_id', $userId)->with(['category', 'wallet'])->latest('date')->get();
        $wallets = Wallet::getWalletsForUser($userId);
        $debts = Debt::where('user_id', $userId)->get();
        $goals = Goal::where('user_id', $userId)->get();

        $filename = 'JAGOAN_Full_Backup_' . Carbon::now()->format('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($categories, $incomes, $expenses, $wallets, $debts, $goals) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#4F46E5" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Title">
   <Font ss:FontName="Calibri" ss:Size="14" ss:Color="#1E1B4B" ss:Bold="1"/>
  </Style>
 </Styles>

 <!-- Worksheet 1: Pemasukan -->
 <Worksheet ss:Name="Pemasukan">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Tanggal (YYYY-MM-DD)</Data></Cell>
    <Cell><Data ss:Type="String">Judul Pemasukan</Data></Cell>
    <Cell><Data ss:Type="String">Jumlah (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Kategori</Data></Cell>
    <Cell><Data ss:Type="String">Rekening/E-Wallet</Data></Cell>
   </Row>
   <?php foreach ($incomes as $inc): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $inc->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $inc->date ? Carbon::parse($inc->date)->format('Y-m-d') : '' ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($inc->title) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $inc->amount ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($inc->category->name ?? 'Gaji & Bonus') ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($inc->wallet->name ?? 'Dompet Cash') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Worksheet 2: Pengeluaran -->
 <Worksheet ss:Name="Pengeluaran">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Tanggal (YYYY-MM-DD)</Data></Cell>
    <Cell><Data ss:Type="String">Judul Pengeluaran</Data></Cell>
    <Cell><Data ss:Type="String">Jumlah (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Kategori</Data></Cell>
    <Cell><Data ss:Type="String">Rekening/E-Wallet</Data></Cell>
   </Row>
   <?php foreach ($expenses as $exp): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $exp->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $exp->date ? Carbon::parse($exp->date)->format('Y-m-d') : '' ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($exp->title) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $exp->amount ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($exp->category->name ?? 'Makanan & Minuman') ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($exp->wallet->name ?? 'Dompet Cash') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Worksheet 3: Rekening & Dompet -->
 <Worksheet ss:Name="Rekening &amp; Dompet">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Nama Dompet</Data></Cell>
    <Cell><Data ss:Type="String">Bank/Provider</Data></Cell>
    <Cell><Data ss:Type="String">No Rekening/Akun</Data></Cell>
    <Cell><Data ss:Type="String">Tipe Dompet</Data></Cell>
    <Cell><Data ss:Type="String">Jenis Kartu</Data></Cell>
    <Cell><Data ss:Type="String">Saldo (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Limit Kredit (Rp)</Data></Cell>
   </Row>
   <?php foreach ($wallets as $w): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $w->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($w->name) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($w->bank_name) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($w->account_number ?? '-') ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $w->type === 'shared' ? 'Dompet Bersama' : 'Dompet Pribadi' ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $w->is_credit ? 'Kartu Kredit / Paylater' : 'Tabungan / Cash' ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $w->balance ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $w->is_credit ? $w->credit_limit : 0 ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Worksheet 4: Kategori -->
 <Worksheet ss:Name="Kategori">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Nama Kategori</Data></Cell>
    <Cell><Data ss:Type="String">Jenis Transaksi</Data></Cell>
   </Row>
   <?php foreach ($categories as $cat): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $cat->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($cat->name) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Worksheet 5: Hutang & Cicilan -->
 <Worksheet ss:Name="Hutang &amp; Cicilan">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Nama Hutang</Data></Cell>
    <Cell><Data ss:Type="String">Jenis Pinjaman</Data></Cell>
    <Cell><Data ss:Type="String">Sisa Hutang (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Cicilan Bulanan (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Tenggat Per Bulan</Data></Cell>
   </Row>
   <?php foreach ($debts as $d): ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $d->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($d->name) ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($d->type) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $d->remaining_amount ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $d->monthly_installment ?></Data></Cell>
    <Cell><Data ss:Type="String">Tanggal <?= $d->due_day ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- Worksheet 6: Target Finansial (Goals) -->
 <Worksheet ss:Name="Target Finansial">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">ID</Data></Cell>
    <Cell><Data ss:Type="String">Nama Target</Data></Cell>
    <Cell><Data ss:Type="String">Target (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Terkumpul (Rp)</Data></Cell>
    <Cell><Data ss:Type="String">Persentase</Data></Cell>
   </Row>
   <?php foreach ($goals as $g): 
      $percent = $g->target > 0 ? round(($g->progress / $g->target) * 100, 1) : 0;
   ?>
   <Row>
    <Cell><Data ss:Type="Number"><?= $g->id ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= htmlspecialchars($g->title) ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $g->target ?></Data></Cell>
    <Cell><Data ss:Type="Number"><?= $g->progress ?></Data></Cell>
    <Cell><Data ss:Type="String"><?= $percent ?>%</Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

</Workbook>
<?php
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCategories()
    {
        $userId = Auth::id();
        $categories = Category::where('user_id', $userId)->get();

        $filename = 'Kategori_Backup_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($categories) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['ID', 'Nama Kategori', 'Jenis Transaksi']);

            foreach ($categories as $cat) {
                fputcsv($file, [
                    $cat->id,
                    $cat->name,
                    $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportIncomes()
    {
        $userId = Auth::id();
        $incomes = Income::where('user_id', $userId)->with(['category', 'wallet'])->latest('date')->get();

        $filename = 'Pemasukan_Backup_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($incomes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['ID', 'Tanggal (YYYY-MM-DD)', 'Judul Pemasukan', 'Jumlah (Rp)', 'Kategori', 'Rekening/E-Wallet']);

            foreach ($incomes as $inc) {
                fputcsv($file, [
                    $inc->id,
                    $inc->date ? Carbon::parse($inc->date)->format('Y-m-d') : '',
                    $inc->title,
                    $inc->amount,
                    $inc->category->name ?? 'Gaji & Bonus',
                    $inc->wallet->name ?? 'Dompet Cash',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExpenses()
    {
        $userId = Auth::id();
        $expenses = Expense::where('user_id', $userId)->with(['category', 'wallet'])->latest('date')->get();

        $filename = 'Pengeluaran_Backup_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['ID', 'Tanggal (YYYY-MM-DD)', 'Judul Pengeluaran', 'Jumlah (Rp)', 'Kategori', 'Rekening/E-Wallet']);

            foreach ($expenses as $exp) {
                fputcsv($file, [
                    $exp->id,
                    $exp->date ? Carbon::parse($exp->date)->format('Y-m-d') : '',
                    $exp->title,
                    $exp->amount,
                    $exp->category->name ?? 'Makanan & Minuman',
                    $exp->wallet->name ?? 'Dompet Cash',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportWallets()
    {
        $userId = Auth::id();
        $wallets = Wallet::getWalletsForUser($userId);

        $filename = 'Rekening_Wallet_Backup_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($wallets) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['ID', 'Nama Dompet', 'Bank/Provider', 'No Rekening/Akun', 'Tipe Dompet', 'Jenis Kartu', 'Saldo/Pokok Hutang (Rp)', 'Limit Kredit (Rp)']);

            foreach ($wallets as $w) {
                fputcsv($file, [
                    $w->id,
                    $w->name,
                    $w->bank_name,
                    $w->account_number ?? '-',
                    $w->type === 'shared' ? 'Dompet Bersama' : 'Dompet Pribadi',
                    $w->is_credit ? 'Kartu Kredit / Paylater' : 'Tabungan / Cash',
                    $w->balance,
                    $w->is_credit ? $w->credit_limit : 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate(Request $request)
    {
        $filename = "Template_Import_JAGOAN.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['Jenis Transaksi (Pemasukan/Pengeluaran)', 'Tanggal (YYYY-MM-DD)', 'Judul Transaksi', 'Jumlah (Rp)', 'Kategori', 'Nama Rekening']);
            fputcsv($file, ['Pemasukan', Carbon::now()->format('Y-m-d'), 'Gaji Bulanan', '10000000', 'Gaji & Bonus', 'BCA']);
            fputcsv($file, ['Pemasukan', Carbon::now()->subDay()->format('Y-m-d'), 'Bonus Proyek', '2500000', 'Freelance & Usaha', 'Dompet Cash']);
            fputcsv($file, ['Pengeluaran', Carbon::now()->format('Y-m-d'), 'Belanja Bulanan Supermarket', '1250000', 'Makanan & Minuman', 'Mandiri']);
            fputcsv($file, ['Pengeluaran', Carbon::now()->subDay()->format('Y-m-d'), 'Bayar Listrik & Wi-Fi', '650000', 'Tagihan & Utilitas', 'GoPay']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ], [
            'file.required' => 'Pilihlah file Excel / CSV yang ingin diunggah.',
        ]);

        $userId = Auth::id();
        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file Excel/CSV.');
        }

        // Skip UTF-8 BOM or sep= header if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $importedIncomes = 0;
        $importedExpenses = 0;
        $rowNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, 'sep=') || str_starts_with($line, '<?xml') || str_contains($line, '<Workbook')) {
                continue;
            }

            // Split by comma or semicolon or tab
            $delimiter = str_contains($line, ';') ? ';' : (str_contains($line, "\t") ? "\t" : ',');
            $row = str_getcsv($line, $delimiter);

            $rowNumber++;

            if (empty($row) || count($row) < 3 || str_contains(implode('', $row), '===')) {
                continue;
            }

            // Skip header row
            if ($rowNumber === 1 || str_contains(strtolower($row[0]), 'jenis') || str_contains(strtolower($row[0]), 'tanggal') || str_contains(strtolower($row[0]), 'id')) {
                continue;
            }

            // Column Mapping Logic:
            // Format Unified: [Jenis Transaksi, Tanggal, Judul, Jumlah, Kategori, Rekening]
            // Format ID Legacy: [ID, Tanggal, Judul, Jumlah, Kategori, Rekening]
            // Format Simple: [Tanggal, Judul, Jumlah, Kategori, Rekening]
            $firstColLower = strtolower(trim($row[0]));

            if ($firstColLower === 'pemasukan' || $firstColLower === 'pengeluaran' || $firstColLower === 'income' || $firstColLower === 'expense') {
                $rowType = str_contains($firstColLower, 'masuk') || $firstColLower === 'income' ? 'income' : 'expense';
                $rawDate = isset($row[1]) ? trim($row[1]) : '';
                $title = isset($row[2]) ? trim($row[2]) : 'Transaksi Import';
                $rawAmount = isset($row[3]) ? trim($row[3]) : '0';
                $catName = isset($row[4]) ? trim($row[4]) : null;
                $walletName = isset($row[5]) ? trim($row[5]) : null;
            } elseif (is_numeric($row[0]) && count($row) >= 6) {
                $rowType = $request->input('type', 'expense');
                $rawDate = trim($row[1]);
                $title = trim($row[2]);
                $rawAmount = trim($row[3]);
                $catName = isset($row[4]) ? trim($row[4]) : null;
                $walletName = isset($row[5]) ? trim($row[5]) : null;
            } else {
                $rowType = $request->input('type', 'expense');
                $rawDate = trim($row[0]);
                $title = isset($row[1]) ? trim($row[1]) : 'Transaksi Import';
                $rawAmount = isset($row[2]) ? trim($row[2]) : '0';
                $catName = isset($row[3]) ? trim($row[3]) : null;
                $walletName = isset($row[4]) ? trim($row[4]) : null;
            }

            $cleanAmount = (float) preg_replace('/[^\d]/', '', $rawAmount);
            if ($cleanAmount <= 0) continue;

            try {
                $parsedDate = Carbon::parse($rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $parsedDate = Carbon::now()->format('Y-m-d');
            }

            // Find or Create Category
            $category = null;
            if ($catName) {
                $category = Category::firstOrCreate([
                    'user_id' => $userId,
                    'name' => $catName,
                    'type' => $rowType,
                ]);
            }

            // Find or Create Wallet
            $wallet = null;
            if ($walletName) {
                $wallet = Wallet::firstOrCreate([
                    'user_id' => $userId,
                    'name' => $walletName,
                ], [
                    'bank_name' => $walletName,
                    'account_number' => '-',
                    'balance' => 0,
                    'color' => '#6366f1',
                    'type' => 'personal',
                ]);
            }

            if ($rowType === 'income') {
                Income::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'amount' => $cleanAmount,
                    'date' => $parsedDate,
                    'category_id' => $category?->id,
                    'wallet_id' => $wallet?->id,
                ]);

                if ($wallet) {
                    $wallet->increment('balance', $cleanAmount);
                }
                $importedIncomes++;
            } else {
                Expense::create([
                    'user_id' => $userId,
                    'title' => $title,
                    'amount' => $cleanAmount,
                    'date' => $parsedDate,
                    'category_id' => $category?->id,
                    'wallet_id' => $wallet?->id,
                ]);

                if ($wallet) {
                    $wallet->decrement('balance', $cleanAmount);
                }
                $importedExpenses++;
            }
        }

        fclose($handle);

        $totalImported = $importedIncomes + $importedExpenses;

        return back()->with('success', "🎉 Berhasil mengimpor {$totalImported} transaksi ({$importedIncomes} Pemasukan, {$importedExpenses} Pengeluaran) dari file Excel!");
    }
}
