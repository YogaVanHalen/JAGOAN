<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Wallet;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        Wallet::ensureDefaultWalletExists(Auth::id());

        $categoryId = $request->input('category_id');
        $period = $request->input('period', 'all');

        $query = Expense::where('user_id', Auth::id());

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
        $expenses = $query->with(['category', 'wallet', 'debt'])->latest()->get();
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());
        $debts = Debt::where('user_id', Auth::id())->where('remaining_amount', '>', 0)->get();

        return view('expense.index', compact('expenses', 'categories', 'wallets', 'debts', 'filteredTotal', 'categoryId', 'period'));
    }

    public function create()
    {
        Wallet::ensureDefaultWalletExists(Auth::id());
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());
        $debts = Debt::where('user_id', Auth::id())->where('remaining_amount', '>', 0)->get();
        return view('expense.create', compact('categories', 'wallets', 'debts'));
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
            'debt_id' => 'nullable|exists:debts,id',
        ]);

        $categoryId = $request->category_id;

        // Auto-assign category if debt is selected and debt has category
        if ($request->debt_id && !$categoryId) {
            $debt = Debt::find($request->debt_id);
            if ($debt && $debt->category_id) {
                $categoryId = $debt->category_id;
            }
        }

        $expense = Expense::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'category_id' => $categoryId,
            'wallet_id' => $request->wallet_id,
            'debt_id' => $request->debt_id,
        ]);

        // Handling Wallet Update
        if ($request->wallet_id) {
            $wallet = Wallet::find($request->wallet_id);
            if ($wallet) {
                if ($wallet->is_credit) {
                    // Belanja via Kartu Kredit/Paylater: MENAMBAH Pokok Hutang Terpakai
                    $wallet->increment('balance', $request->amount);

                    // Sync ke menu Hutang & Pinjaman
                    $linkedDebt = Debt::where('user_id', Auth::id())
                        ->where('name', 'like', '%' . $wallet->name . '%')
                        ->first();

                    if ($linkedDebt) {
                        $linkedDebt->increment('remaining_amount', $request->amount);
                    }
                } else {
                    // Belanja via Tabungan / Cash Biasa: MENGURANGI Saldo Uang Riil
                    $wallet->decrement('balance', $request->amount);
                }
            }
        }

        // Handling Debt Repayment (Pelunasan Hutang Tetap seperti KPR/KTA/KKB)
        if ($request->debt_id) {
            $debt = Debt::find($request->debt_id);
            if ($debt && $debt->user_id === Auth::id()) {
                $newRemaining = max(0, $debt->remaining_amount - $request->amount);
                // Hanya kurangi tenor jika bukan revolver / kartu kredit
                $isRevolving = in_array(strtolower($debt->type), ['kartu kredit', 'paylater', 'pinjol', 'paylater / pinjol']);
                $newTenor = $isRevolving ? $debt->remaining_tenor_months : max(0, $debt->remaining_tenor_months - 1);

                $debt->update([
                    'remaining_amount' => $newRemaining,
                    'remaining_tenor_months' => $newTenor,
                ]);
            }
        }

        return redirect()->route('expense.index')->with('success', 'Pengeluaran ditambahkan!');
    }

    public function edit(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $expense->date = \Carbon\Carbon::parse($expense->date);
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();
        $wallets = Wallet::getWalletsForUser(Auth::id());
        $debts = Debt::where('user_id', Auth::id())->get();
        return view('expense.edit', compact('expense', 'categories', 'wallets', 'debts'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
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
            'debt_id' => 'nullable|exists:debts,id',
        ]);

        // Revert old wallet expense
        if ($expense->wallet_id) {
            $oldWallet = Wallet::find($expense->wallet_id);
            if ($oldWallet) {
                if ($oldWallet->is_credit) {
                    $oldWallet->decrement('balance', $expense->amount);
                    $linkedDebt = Debt::where('user_id', Auth::id())->where('name', 'like', '%' . $oldWallet->name . '%')->first();
                    if ($linkedDebt) {
                        $linkedDebt->decrement('remaining_amount', $expense->amount);
                    }
                } else {
                    $oldWallet->increment('balance', $expense->amount);
                }
            }
        }

        // Revert old debt repayment
        if ($expense->debt_id) {
            $oldDebt = Debt::find($expense->debt_id);
            if ($oldDebt) {
                $oldDebt->increment('remaining_amount', $expense->amount);
                $isRevolving = in_array(strtolower($oldDebt->type), ['kartu kredit', 'paylater', 'pinjol', 'paylater / pinjol']);
                if (!$isRevolving) {
                    $oldDebt->increment('remaining_tenor_months', 1);
                }
            }
        }

        $categoryId = $request->category_id;
        if ($request->debt_id && !$categoryId) {
            $debt = Debt::find($request->debt_id);
            if ($debt && $debt->category_id) {
                $categoryId = $debt->category_id;
            }
        }

        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'category_id' => $categoryId,
            'wallet_id' => $request->wallet_id,
            'debt_id' => $request->debt_id,
        ]);

        // Deduct new wallet expense
        if ($request->wallet_id) {
            $newWallet = Wallet::find($request->wallet_id);
            if ($newWallet && $newWallet->user_id === Auth::id()) {
                if ($newWallet->is_credit) {
                    $newWallet->increment('balance', $request->amount);
                    $linkedDebt = Debt::where('user_id', Auth::id())->where('name', 'like', '%' . $newWallet->name . '%')->first();
                    if ($linkedDebt) {
                        $linkedDebt->increment('remaining_amount', $request->amount);
                    }
                } else {
                    $newWallet->decrement('balance', $request->amount);
                }
            }
        }

        // Deduct new debt repayment
        if ($request->debt_id) {
            $newDebt = Debt::find($request->debt_id);
            if ($newDebt && $newDebt->user_id === Auth::id()) {
                $newRemaining = max(0, $newDebt->remaining_amount - $request->amount);
                $isRevolving = in_array(strtolower($newDebt->type), ['kartu kredit', 'paylater', 'pinjol', 'paylater / pinjol']);
                $newTenor = $isRevolving ? $newDebt->remaining_tenor_months : max(0, $newDebt->remaining_tenor_months - 1);
                $newDebt->update([
                    'remaining_amount' => $newRemaining,
                    'remaining_tenor_months' => $newTenor,
                ]);
            }
        }

        return redirect()->route('expense.index')->with('success', 'Pengeluaran diperbarui!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Revert wallet expense
        if ($expense->wallet_id) {
            $wallet = Wallet::find($expense->wallet_id);
            if ($wallet) {
                if ($wallet->is_credit) {
                    $wallet->decrement('balance', $expense->amount);
                    $linkedDebt = Debt::where('user_id', Auth::id())->where('name', 'like', '%' . $wallet->name . '%')->first();
                    if ($linkedDebt) {
                        $linkedDebt->decrement('remaining_amount', $expense->amount);
                    }
                } else {
                    $wallet->increment('balance', $expense->amount);
                }
            }
        }

        // Revert debt repayment
        if ($expense->debt_id) {
            $debt = Debt::find($expense->debt_id);
            if ($debt) {
                $debt->increment('remaining_amount', $expense->amount);
                $isRevolving = in_array(strtolower($debt->type), ['kartu kredit', 'paylater', 'pinjol', 'paylater / pinjol']);
                if (!$isRevolving) {
                    $debt->increment('remaining_tenor_months', 1);
                }
            }
        }

        $expense->delete();

        return redirect()->route('expense.index')->with('success', 'Pengeluaran dihapus!');
    }
}
