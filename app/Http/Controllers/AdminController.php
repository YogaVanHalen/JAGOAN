<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalWallets = Wallet::count();
        $totalIncomes = (float) Income::sum('amount');
        $totalExpenses = (float) Expense::sum('amount');
        $totalDebts = (float) Debt::sum('remaining_amount');

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalWallets',
            'totalIncomes',
            'totalExpenses',
            'totalDebts',
            'recentUsers'
        ));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|alpha_dash|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,admin,user',
        ]);

        $baseUsername = $request->username ? strtolower($request->username) : \Illuminate\Support\Str::slug(explode('@', $request->email)[0], '');
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower($username),
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', "Pengguna {$user->name} (@{$user->username}) berhasil dibuat sebagai " . strtoupper($user->role) . ".");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:superadmin,admin,user',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', "Pengguna {$user->name} telah dihapus dari sistem.");
    }

    public function toggleRole(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $roles = ['superadmin', 'admin', 'user'];
        $currentIndex = array_search($user->role, $roles);
        $nextIndex = ($currentIndex === false || $currentIndex === count($roles) - 1) ? 1 : $currentIndex + 1;
        $newRole = $roles[$nextIndex];

        $user->update(['role' => $newRole]);

        return back()->with('success', "Role pengguna {$user->name} telah diubah menjadi " . strtoupper($newRole) . ".");
    }
}
