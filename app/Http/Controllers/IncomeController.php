<?php
namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        Wallet::ensureDefaultWalletExists(Auth::id());

        $categoryId = $request->input('category_id');
        $period = $request->input('period', 'all');

        $query = Income::where('user_id', Auth::id());

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $now = Carbon::now();
        if ($period === 'daily') {
            $query->whereDate('date', Carbon::today());
        } elseif ($period === 'weekly') {
            $query->whereBetween('date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
        } elseif ($period === 'monthly') {
            $query->whereYear('date', $now->year)->whereMonth('date', $now->month);
        } elseif ($period === 'yearly') {
            $query->whereYear('date', $now->year);
        }

        $filteredTotal = (float) $query->sum('amount');
        $incomes = $query->with(['category', 'wallet'])->latest()->get();
        $categories = Category::where('user_id', Auth::id())->where('type', 'income')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());

        return view('income.index', compact('incomes', 'categories', 'wallets', 'filteredTotal', 'categoryId', 'period'));
    }

    public function create()
    {
        Wallet::ensureDefaultWalletExists(Auth::id());
        $categories = Category::where('user_id', Auth::id())->where('type', 'income')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());
        return view('income.create', compact('categories', 'wallets'));
    }

    public function store(Request $request)
    {
        $cleanAmount = preg_replace('/[^\d]/', '', $request->amount);

        $request->merge([
            'amount' => $cleanAmount
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);

        $income = Income::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'category_id' => $request->category_id,
            'wallet_id' => $request->wallet_id,
        ]);

        // Tambahkan saldo ke wallet/rekening penampung
        if ($request->wallet_id) {
            $wallet = Wallet::find($request->wallet_id);
            if ($wallet) {
                $wallet->increment('balance', $request->amount);
            }
        }

        return redirect()->route('income.index')->with('success', 'Pemasukan ditambahkan!');
    }

    public function edit(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $income->date = \Carbon\Carbon::parse($income->date);

        $categories = Category::where('user_id', Auth::id())->where('type', 'income')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());
        return view('income.edit', compact('income', 'categories', 'wallets'));
    }

    public function update(Request $request, Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $cleanAmount = preg_replace('/[^\d]/', '', $request->amount);
        $request->merge([
            'amount' => $cleanAmount
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'wallet_id' => 'nullable|exists:wallets,id',
        ]);

        // Revert old wallet amount
        if ($income->wallet_id) {
            $oldWallet = Wallet::find($income->wallet_id);
            if ($oldWallet) {
                $oldWallet->decrement('balance', $income->amount);
            }
        }

        $income->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'category_id' => $request->category_id,
            'wallet_id' => $request->wallet_id,
        ]);

        // Add new wallet amount
        if ($request->wallet_id) {
            $newWallet = Wallet::find($request->wallet_id);
            if ($newWallet) {
                $newWallet->increment('balance', $request->amount);
            }
        }

        return redirect()->route('income.index')->with('success', 'Pemasukan diperbarui!');
    }

    public function destroy(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Revert wallet amount
        if ($income->wallet_id) {
            $wallet = Wallet::find($income->wallet_id);
            if ($wallet) {
                $wallet->decrement('balance', $income->amount);
            }
        }

        $income->delete();

        return redirect()->route('income.index')->with('success', 'Pemasukan dihapus!');
    }
}