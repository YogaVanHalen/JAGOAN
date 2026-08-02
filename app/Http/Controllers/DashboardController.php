<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        \App\Models\Wallet::ensureDefaultWalletExists($userId);
        \App\Models\Debt::checkAndAccrueInterestForUser($userId);
    
        // Ambil parameter filter
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $keyword = $request->input('keyword');
        $categoryId = $request->input('category_id');
    
        // Query income dengan filter
        $incomeQuery = Income::where('user_id', $userId);
        if ($fromDate) {
            $incomeQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $incomeQuery->whereDate('date', '<=', $toDate);
        }
        if ($keyword) {
            $incomeQuery->where('title', 'like', '%' . $keyword . '%');
        }
        if ($categoryId) {
            $incomeQuery->where('category_id', $categoryId);
        }
    
        // Query expense dengan filter
        $expenseQuery = Expense::where('user_id', $userId);
        if ($fromDate) {
            $expenseQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $expenseQuery->whereDate('date', '<=', $toDate);
        }
        if ($keyword) {
            $expenseQuery->where('title', 'like', '%' . $keyword . '%');
        }
        if ($categoryId) {
            $expenseQuery->where('category_id', $categoryId);
        }
    
        \App\Models\Category::ensureDefaultCategoriesExist($userId);
        $categories = Category::where('user_id', $userId)->get();
        $wallets = Wallet::getWalletsForUser($userId);

        // 1. Total Saldo Akumulasi (Sisa Uang Riil Tersimpan di Semua Wadah/Rekening)
        $totalWalletBalance = (float) $wallets->sum('balance');

        // 2. Transaksi & Cashflow Bulan Ini (Otomatis Reset Rp 0 setiap Awal Bulan Baru)
        $currentMonthIncome = (float) Income::where('user_id', $userId)
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');

        $currentMonthExpense = (float) Expense::where('user_id', $userId)
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');

        $monthlyCashflow = $currentMonthIncome - $currentMonthExpense;

        // 3. Transaksi Bulan Lalu & Perbandingan Persentase (% Growth vs Last Month)
        $lastMonthObj = Carbon::now()->subMonth();
        $lastMonthIncome = (float) Income::where('user_id', $userId)
            ->whereYear('date', $lastMonthObj->year)
            ->whereMonth('date', $lastMonthObj->month)
            ->sum('amount');

        $lastMonthExpense = (float) Expense::where('user_id', $userId)
            ->whereYear('date', $lastMonthObj->year)
            ->whereMonth('date', $lastMonthObj->month)
            ->sum('amount');

        $incomeChangePct = 0;
        if ($lastMonthIncome > 0) {
            $incomeChangePct = round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1);
        } else {
            $incomeChangePct = $currentMonthIncome > 0 ? 100 : 0;
        }

        $expenseChangePct = 0;
        if ($lastMonthExpense > 0) {
            $expenseChangePct = round((($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1);
        } else {
            $expenseChangePct = $currentMonthExpense > 0 ? 100 : 0;
        }

        // Calculate Expense-to-Income Ratio for current month (%)
        $expenseRatio = 0;
        if ($currentMonthIncome > 0) {
            $expenseRatio = min(100, round(($currentMonthExpense / $currentMonthIncome) * 100, 1));
        } else {
            $expenseRatio = $currentMonthExpense > 0 ? 100 : 0;
        }

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');        
        $balance = $totalIncome - $totalExpense;
    
        // Recent transactions dengan eager load category
        $recentIncome = (clone $incomeQuery)->with('category')->latest()->get()->map(function ($item) {
            $item->type = 'income';
            return $item;
        });
    
        $recentExpense = (clone $expenseQuery)->with('category')->latest()->get()->map(function ($item) {
            $item->type = 'expense';
            return $item;
        });
    
        $recentTransactions = $recentIncome->concat($recentExpense)->sortByDesc('date')->values();

        // --- 1. GRAFIK TREN KEUANGAN (Harian/Rentang Waktu) ---
        $startDateTrend = $fromDate ? Carbon::parse($fromDate) : Carbon::now()->subDays(29);
        $endDateTrend = $toDate ? Carbon::parse($toDate) : Carbon::now();

        $trendDates = [];
        $trendIncome = [];
        $trendExpense = [];

        $incomeByDate = (clone $incomeQuery)
            ->selectRaw('DATE(date) as formatted_date, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('total', 'formatted_date');

        $expenseByDate = (clone $expenseQuery)
            ->selectRaw('DATE(date) as formatted_date, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('total', 'formatted_date');

        $curr = clone $startDateTrend;
        while ($curr <= $endDateTrend) {
            $dStr = $curr->format('Y-m-d');
            $dLabel = $curr->format('d M');
            $trendDates[] = $dLabel;
            $trendIncome[] = (float) ($incomeByDate[$dStr] ?? 0);
            $trendExpense[] = (float) ($expenseByDate[$dStr] ?? 0);
            $curr->addDay();
        }

        // --- 2. GRAFIK PENGELUARAN PER KATEGORI ---
        $categoryBreakdownRaw = DB::table('expenses')
            ->where('expenses.user_id', $userId)
            ->when($fromDate, fn($q) => $q->whereDate('expenses.date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expenses.date', '<=', $toDate))
            ->when($keyword, fn($q) => $q->where('expenses.title', 'like', '%' . $keyword . '%'))
            ->when($categoryId, fn($q) => $q->where('expenses.category_id', $categoryId))
            ->leftJoin('categories', 'expenses.category_id', '=', 'categories.id')
            ->selectRaw('COALESCE(categories.name, "Lain-lain") as category_name, SUM(expenses.amount) as total')
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categoryBreakdownRaw->pluck('category_name')->toArray();
        $categoryData = $categoryBreakdownRaw->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // --- 3. GRAFIK ARUS KAS BULANAN (6 Bulan Terakhir) ---
        $monthlyLabels = [];
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthObj = Carbon::now()->subMonths($i);
            $year = $monthObj->year;
            $month = $monthObj->month;
            $mLabel = $monthObj->format('M Y');

            $mInc = Income::where('user_id', $userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            $mExp = Expense::where('user_id', $userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            $monthlyLabels[] = $mLabel;
            $monthlyIncome[] = (float) $mInc;
            $monthlyExpense[] = (float) $mExp;
        }

        // --- 4. GRAFIK PROGRESS FINANCIAL GOALS ---
        $goals = Goal::where('user_id', $userId)->get();
        $goalTitles = [];
        $goalProgress = [];
        $goalTargets = [];
        $goalPercentages = [];

        foreach ($goals as $goal) {
            $pct = $goal->target > 0 ? min(100, round(($goal->progress / $goal->target) * 100, 1)) : 0;
            $goalTitles[] = $goal->title;
            $goalProgress[] = (float) $goal->progress;
            $goalTargets[] = (float) $goal->target;
            $goalPercentages[] = $pct;
        }

        $chartData = [
            'trend' => [
                'labels' => $trendDates,
                'income' => $trendIncome,
                'expense' => $trendExpense,
            ],
            'categories' => [
                'labels' => $categoryLabels,
                'data' => $categoryData,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'income' => $monthlyIncome,
                'expense' => $monthlyExpense,
            ],
            'goals' => [
                'titles' => $goalTitles,
                'progress' => $goalProgress,
                'targets' => $goalTargets,
                'percentages' => $goalPercentages,
            ],
        ];

        $debts = \App\Models\Debt::where('user_id', $userId)->get();

        return view('dashboard', compact(
            'balance',
            'totalIncome',
            'totalExpense',
            'recentTransactions',
            'categories',
            'chartData',
            'goals',
            'wallets',
            'debts',
            'totalWalletBalance',
            'currentMonthIncome',
            'currentMonthExpense',
            'monthlyCashflow',
            'expenseRatio',
            'incomeChangePct',
            'expenseChangePct'
        ));
    }    
}