<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Debt;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class WalletController extends Controller
{
    public function index()
    {
        Debt::checkAndAccrueInterestForUser(Auth::id());
        
        $allWallets = Wallet::getWalletsForUser(Auth::id());
        $myWallets = $allWallets->filter(fn($w) => (int)$w->user_id === (int)Auth::id());
        $sharedWithMe = $allWallets->filter(fn($w) => (int)$w->user_id !== (int)Auth::id());

        return view('wallets.index', compact('myWallets', 'sharedWithMe'));
    }

    public function create()
    {
        $bankOptions = [
            'BCA', 'Mandiri', 'BRI', 'BNI', 'Bank Jago', 'Seabank', 'BSI', 'CIMB Niaga', 'Danamon', 'Permata',
            'GoPay', 'OVO', 'ShopeePay', 'Dana', 'LinkAja', 'Kredivo', 'Akulaku', 'Tunai / Cash'
        ];
        $networks = ['Visa', 'Mastercard', 'Amex', 'JCB', 'GPN', 'Paylater / Pinjol'];

        return view('wallets.create', compact('bankOptions', 'networks'));
    }

    public function store(Request $request)
    {
        $cleanBalance = preg_replace('/[^\d]/', '', $request->balance ?? '0');
        $cleanLimit = preg_replace('/[^\d]/', '', $request->credit_limit ?? '0');

        $request->merge([
            'balance' => $cleanBalance,
            'credit_limit' => $cleanLimit,
            'is_credit' => $request->has('is_credit') ? true : false,
            'auto_accrue_interest' => $request->has('auto_accrue_interest') ? true : false,
            'type' => $request->input('type', 'personal'),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:personal,shared',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'is_credit' => 'boolean',
            'card_network' => 'nullable|string|max:255',
            'first_four_digits' => 'nullable|string|max:30',
            'interest_rate_percent' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'auto_accrue_interest' => 'boolean',
        ]);

        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'balance' => $request->balance,
            'color' => $request->color ?? ($request->is_credit ? '#dc2626' : '#6366f1'),
            'is_credit' => $request->is_credit,
            'card_network' => $request->card_network,
            'first_four_digits' => $request->first_four_digits,
            'interest_rate_percent' => $request->interest_rate_percent ?? 0,
            'credit_limit' => $request->credit_limit ?? 0,
            'auto_accrue_interest' => $request->auto_accrue_interest,
        ]);

        // If it's a credit card/paylater, automatically sync to Debts table as revolving debt!
        if ($wallet->is_credit) {
            $catHutang = Category::firstOrCreate(['name' => 'Cicilan & Hutang', 'user_id' => Auth::id(), 'type' => 'expense']);

            Debt::create([
                'user_id' => Auth::id(),
                'category_id' => $catHutang->id,
                'name' => $wallet->name,
                'type' => $wallet->card_network ?? 'Kartu Kredit',
                'initial_amount' => $wallet->credit_limit > 0 ? $wallet->credit_limit : $wallet->balance,
                'remaining_amount' => $wallet->balance, // Pokok hutang terpakai
                'monthly_installment' => $wallet->balance,
                'tenor_months' => 1,
                'remaining_tenor_months' => 1,
                'due_day' => 10,
                'color' => $wallet->color,
                'interest_rate_percent' => $wallet->interest_rate_percent,
                'auto_accrue_interest' => $wallet->auto_accrue_interest,
            ]);
        }

        return redirect()->route('wallets.index')->with('success', 'Rekening/E-Wallet/Kartu Kredit berhasil dibuat!');
    }

    public function addMember(Request $request, Wallet $wallet)
    {
        if ((int)$wallet->user_id !== (int)Auth::id() && !Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya pemilik dompet atau Superadmin yang dapat mengundang anggota.');
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $userToAdd = User::where('email', $request->email)->first();

        // Jika user belum terdaftar, otomatis buatkan akun user baru untuk anggota ini!
        if (!$userToAdd) {
            $userName = $request->name ?: explode('@', $request->email)[0];
            $userPassword = $request->password ? \Illuminate\Support\Facades\Hash::make($request->password) : \Illuminate\Support\Facades\Hash::make('123456');

            $userToAdd = User::create([
                'name' => ucfirst($userName),
                'email' => $request->email,
                'password' => $userPassword,
                'role' => 'user',
            ]);
        }

        if ($userToAdd->id === (int)$wallet->user_id) {
            return back()->with('error', 'Pengguna ini adalah pemilik dompet.');
        }

        if ($wallet->members()->where('users.id', $userToAdd->id)->exists()) {
            return back()->with('error', "Pengguna {$userToAdd->name} sudah menjadi anggota dompet ini.");
        }

        $wallet->members()->attach($userToAdd->id, ['role' => 'member']);
        $wallet->update(['type' => 'shared']);

        return back()->with('success', "Berhasil menambahkan {$userToAdd->name} ({$userToAdd->email}) ke dompet bersama ini!");
    }

    public function removeMember(Wallet $wallet, User $user)
    {
        if ((int)$wallet->user_id !== (int)Auth::id()) {
            return back()->with('error', 'Hanya pemilik dompet yang dapat mengeluarkan anggota.');
        }

        $wallet->members()->detach($user->id);

        if ($wallet->members()->count() === 0) {
            $wallet->update(['type' => 'personal']);
        }

        return back()->with('success', "Anggota {$user->name} berhasil dikeluarkan dari dompet.");
    }

    public function edit(Wallet $wallet)
    {
        if ((int)$wallet->user_id !== (int)Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $bankOptions = [
            'BCA', 'Mandiri', 'BRI', 'BNI', 'Bank Jago', 'Seabank', 'BSI', 'CIMB Niaga', 'Danamon', 'Permata',
            'GoPay', 'OVO', 'ShopeePay', 'Dana', 'LinkAja', 'Kredivo', 'Akulaku', 'Tunai / Cash'
        ];
        $networks = ['Visa', 'Mastercard', 'Amex', 'JCB', 'GPN', 'Paylater / Pinjol'];

        return view('wallets.edit', compact('wallet', 'bankOptions', 'networks'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        if ((int)$wallet->user_id !== (int)Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $cleanBalance = preg_replace('/[^\d]/', '', $request->balance ?? '0');
        $cleanLimit = preg_replace('/[^\d]/', '', $request->credit_limit ?? '0');

        $request->merge([
            'balance' => $cleanBalance,
            'credit_limit' => $cleanLimit,
            'is_credit' => $request->has('is_credit') ? true : false,
            'auto_accrue_interest' => $request->has('auto_accrue_interest') ? true : false,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'is_credit' => 'boolean',
            'card_network' => 'nullable|string|max:255',
            'first_four_digits' => 'nullable|string|max:30',
            'interest_rate_percent' => 'nullable|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'auto_accrue_interest' => 'boolean',
        ]);

        $wallet->update([
            'name' => $request->name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'balance' => $request->balance,
            'color' => $request->color ?? $wallet->color,
            'is_credit' => $request->is_credit,
            'card_network' => $request->card_network,
            'first_four_digits' => $request->first_four_digits,
            'interest_rate_percent' => $request->interest_rate_percent ?? 0,
            'credit_limit' => $request->credit_limit ?? 0,
            'auto_accrue_interest' => $request->auto_accrue_interest,
        ]);

        // Sync to corresponding debt
        if ($wallet->is_credit) {
            $debt = Debt::where('user_id', Auth::id())->where('name', $wallet->name)->first();
            if ($debt) {
                $debt->update([
                    'interest_rate_percent' => $wallet->interest_rate_percent,
                    'auto_accrue_interest' => $wallet->auto_accrue_interest,
                ]);
            }
        }

        return redirect()->route('wallets.index')->with('success', 'Rekening/E-Wallet diperbarui!');
    }

    public function destroy(Wallet $wallet)
    {
        if ((int)$wallet->user_id !== (int)Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'Rekening/E-Wallet dihapus!');
    }

    public function familyMembers()
    {
        $myWallets = Wallet::where('user_id', Auth::id())->get();
        $walletIds = $myWallets->pluck('id');

        $members = User::whereHas('sharedWallets', function ($q) use ($walletIds) {
            $q->whereIn('wallets.id', $walletIds);
        })->with(['sharedWallets' => function ($q) use ($walletIds) {
            $q->whereIn('wallets.id', $walletIds);
        }])->get();

        return view('family.index', compact('myWallets', 'members'));
    }

    public function storeFamilyMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'wallet_ids' => 'required|array|min:1',
            'wallet_ids.*' => 'exists:wallets,id',
        ], [
            'wallet_ids.required' => 'Pilihlah minimal 1 dompet yang ingin dibagikan ke anggota ini.',
        ]);

        $myWalletIds = Wallet::where('user_id', Auth::id())->whereIn('id', $request->wallet_ids)->pluck('id');
        if ($myWalletIds->isEmpty()) {
            return back()->with('error', 'Dompet yang dipilih tidak valid atau bukan milik Anda.');
        }

        $userToAdd = User::where('email', $request->email)->first();

        if (!$userToAdd) {
            $userName = $request->name ?: explode('@', $request->email)[0];
            $userPassword = $request->password ? \Illuminate\Support\Facades\Hash::make($request->password) : \Illuminate\Support\Facades\Hash::make('123456');

            $baseUsername = \Illuminate\Support\Str::slug(explode('@', $request->email)[0], '');
            if (empty($baseUsername)) {
                $baseUsername = 'user';
            }
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $userToAdd = User::create([
                'name' => ucfirst($userName),
                'username' => strtolower($username),
                'email' => $request->email,
                'password' => $userPassword,
                'role' => 'user',
            ]);
        }

        foreach ($myWalletIds as $wId) {
            $wallet = Wallet::find($wId);
            if ($wallet && (int)$wallet->user_id !== (int)$userToAdd->id) {
                if (!$wallet->members()->where('users.id', $userToAdd->id)->exists()) {
                    $wallet->members()->attach($userToAdd->id, ['role' => 'member']);
                    $wallet->update(['type' => 'shared']);
                }
            }
        }

        return back()->with('success', "Berhasil mendaftarkan/menghubungkan {$userToAdd->name} ({$userToAdd->email}) ke dompet pilihan Anda!");
    }
}
