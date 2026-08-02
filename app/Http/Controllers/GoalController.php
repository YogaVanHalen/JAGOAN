<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->with('debt')->get();
        foreach ($goals as $goal) {
            if ($goal->debt_id) {
                $goal->syncFromDebt();
            }
        }
        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $cleanTarget = preg_replace('/[^\d]/', '', $request->target ?? '0');
        $cleanProgress = preg_replace('/[^\d]/', '', $request->progress ?? '0');

        $request->merge([
            'target' => $cleanTarget,
            'progress' => $cleanProgress,
        ]);

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'target' => 'required|integer|min:1',
            'progress' => 'nullable|integer|min:0',
        ]);

        Goal::create([
            'title' => $request->title,
            'description' => $request->description,
            'target' => $request->target,
            'progress' => $request->progress ?? 0,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('goals.index')->with('success', 'Goal berhasil dibuat!');
    }

    public function edit(Goal $goal)
    {
        // Pastikan hanya user yang memiliki goal yang bisa mengeditnya
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal)
    {
        // Pastikan hanya goal milik user yang bisa diupdate
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi inputan
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'target' => 'required|integer|min:1',
        ]);

        // Update data goal
        $goal->update($request->only('title', 'description', 'target'));

        return redirect()->route('goals.index')->with('success', 'Goal berhasil diperbarui!');
    }

    public function destroy(Goal $goal)
    {
        // Pastikan hanya goal milik user yang bisa dihapus
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Goal berhasil dihapus!');
    }

    public function updateProgress(Request $request, $goalId)
    {
        $goal = Goal::findOrFail($goalId);

        $cleanAmount = preg_replace('/[^\d]/', '', $request->progress_update);
        $progressToAdd = (int) $cleanAmount;

        $request->merge([
            'progress_update' => $progressToAdd
        ]);

        $request->validate([
            'progress_update' => 'required|numeric|min:0',
        ]);

        $newProgress = $goal->progress + $progressToAdd;

        if ($newProgress > $goal->target) {
            return redirect()->back()->with('error', 'Progress tidak boleh melebihi target.');
        }

        $goal->update([
            'progress' => $newProgress,
        ]);

        \App\Models\Transaction::create([
            'goal_id' => $goal->id,
            'title' => 'Update Progress',
            'amount' => $progressToAdd,
            'date' => now(),
        ]);

        return redirect()->route('goals.show', $goal->id)->with('success', 'Progress berhasil diperbarui dan transaksi disimpan!');
    }    

    public function show($id)
    {
        $goal = Goal::findOrFail($id);
        $transactions = $goal->transactions;

        return view('goals.show', compact('goal', 'transactions'));
    }

    public function storeTransaction(Request $request, $goalId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $goal = Goal::findOrFail($goalId);

        $goal->transactions()->create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('goals.show', $goal->id)->with('success', 'Transaksi berhasil ditambahkan!');
    }
}
