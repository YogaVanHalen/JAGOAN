<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    public function index()
    {
        Debt::checkAndAccrueInterestForUser(Auth::id());
        $debts = Debt::where('user_id', Auth::id())->with(['category', 'goal'])->latest()->get();
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();
        return view('debts.index', compact('debts', 'categories'));
    }

    public function create()
    {
        $types = ['KPR', 'Kartu Kredit', 'Kredit Kendaraan (KKB)', 'Pinjaman Bank', 'Paylater / Pinjol', 'Lainnya'];
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();
        return view('debts.create', compact('types', 'categories'));
    }

    public function store(Request $request)
    {
        $cleanInitial = preg_replace('/[^\d]/', '', $request->initial_amount ?? '0');
        $cleanRemaining = preg_replace('/[^\d]/', '', $request->remaining_amount ?? $cleanInitial);
        $cleanInstallment = preg_replace('/[^\d]/', '', $request->monthly_installment ?? '0');

        $request->merge([
            'initial_amount' => $cleanInitial,
            'remaining_amount' => $cleanRemaining,
            'monthly_installment' => $cleanInstallment,
            'auto_accrue_interest' => $request->has('auto_accrue_interest') ? true : false,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'initial_amount' => 'required|numeric|min:1',
            'remaining_amount' => 'required|numeric|min:0',
            'monthly_installment' => 'required|numeric|min:0',
            'tenor_months' => 'required|integer|min:1',
            'remaining_tenor_months' => 'required|integer|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'category_id' => 'nullable|exists:categories,id',
            'color' => 'nullable|string|max:7',
            'interest_rate_percent' => 'nullable|numeric|min:0',
            'auto_accrue_interest' => 'boolean',
        ]);

        $debt = Debt::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'type' => $request->type,
            'initial_amount' => $request->initial_amount,
            'remaining_amount' => $request->remaining_amount,
            'monthly_installment' => $request->monthly_installment,
            'tenor_months' => $request->tenor_months,
            'remaining_tenor_months' => $request->remaining_tenor_months,
            'due_day' => $request->due_day,
            'color' => $request->color ?? '#ef4444',
            'interest_rate_percent' => $request->interest_rate_percent ?? 0,
            'auto_accrue_interest' => $request->auto_accrue_interest,
        ]);

        // Buat Goal otomatis jika opsi dicentang
        if ($request->has('make_goal')) {
            $initial = (float) $debt->initial_amount;
            $remaining = (float) $debt->remaining_amount;
            $progress = max(0, $initial - $remaining);

            Goal::create([
                'user_id' => Auth::id(),
                'debt_id' => $debt->id,
                'title' => '🎯 Pelunasan: ' . $debt->name,
                'description' => 'Target pelunasan ' . $debt->type . ' (' . $debt->name . ')',
                'target' => $initial,
                'progress' => $progress,
            ]);
        }

        return redirect()->route('debts.index')->with('success', 'Catatan Hutang/Pinjaman berhasil ditambahkan!');
    }

    public function edit(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $types = ['KPR', 'Kartu Kredit', 'Kredit Kendaraan (KKB)', 'Pinjaman Bank', 'Paylater / Pinjol', 'Lainnya'];
        $categories = Category::where('user_id', Auth::id())->where('type', 'expense')->get();

        return view('debts.edit', compact('debt', 'types', 'categories'));
    }

    public function update(Request $request, Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $cleanInitial = preg_replace('/[^\d]/', '', $request->initial_amount ?? '0');
        $cleanRemaining = preg_replace('/[^\d]/', '', $request->remaining_amount ?? '0');
        $cleanInstallment = preg_replace('/[^\d]/', '', $request->monthly_installment ?? '0');

        $request->merge([
            'initial_amount' => $cleanInitial,
            'remaining_amount' => $cleanRemaining,
            'monthly_installment' => $cleanInstallment,
            'auto_accrue_interest' => $request->has('auto_accrue_interest') ? true : false,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'initial_amount' => 'required|numeric|min:1',
            'remaining_amount' => 'required|numeric|min:0',
            'monthly_installment' => 'required|numeric|min:0',
            'tenor_months' => 'required|integer|min:1',
            'remaining_tenor_months' => 'required|integer|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'category_id' => 'nullable|exists:categories,id',
            'color' => 'nullable|string|max:7',
            'interest_rate_percent' => 'nullable|numeric|min:0',
            'auto_accrue_interest' => 'boolean',
        ]);

        $debt->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'type' => $request->type,
            'initial_amount' => $request->initial_amount,
            'remaining_amount' => $request->remaining_amount,
            'monthly_installment' => $request->monthly_installment,
            'tenor_months' => $request->tenor_months,
            'remaining_tenor_months' => $request->remaining_tenor_months,
            'due_day' => $request->due_day,
            'color' => $request->color ?? $debt->color,
            'interest_rate_percent' => $request->interest_rate_percent ?? 0,
            'auto_accrue_interest' => $request->auto_accrue_interest,
        ]);

        if ($request->has('make_goal') && !$debt->goal) {
            $initial = (float) $debt->initial_amount;
            $remaining = (float) $debt->remaining_amount;
            $progress = max(0, $initial - $remaining);

            Goal::create([
                'user_id' => Auth::id(),
                'debt_id' => $debt->id,
                'title' => '🎯 Pelunasan: ' . $debt->name,
                'description' => 'Target pelunasan ' . $debt->type . ' (' . $debt->name . ')',
                'target' => $initial,
                'progress' => $progress,
            ]);
        } else {
            $debt->syncGoalProgress();
        }

        return redirect()->route('debts.index')->with('success', 'Catatan Hutang/Pinjaman diperbarui!');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $debt->delete();
        return redirect()->route('debts.index')->with('success', 'Catatan Hutang/Pinjaman dihapus!');
    }

    public function convertToGoal(Debt $debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if (!$debt->goal) {
            $initial = (float) $debt->initial_amount;
            $remaining = (float) $debt->remaining_amount;
            $progress = max(0, $initial - $remaining);

            Goal::create([
                'user_id' => Auth::id(),
                'debt_id' => $debt->id,
                'title' => '🎯 Pelunasan: ' . $debt->name,
                'description' => 'Target pelunasan ' . $debt->type . ' (' . $debt->name . ')',
                'target' => $initial,
                'progress' => $progress,
            ]);
        }

        return redirect()->route('goals.index')->with('success', 'Hutang berhasil dijadikan Target Finansial (Goal)!');
    }
}
