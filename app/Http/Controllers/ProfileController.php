<?php
// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // Menampilkan halaman untuk mengedit profil
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    // Mengupdate profil pengguna
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Untuk gambar profil
        ]);

        $user->name = $request->name;
        $user->username = strtolower($request->username);
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle upload avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui!');
    }

    public function destroy(Request $request)
{
    $user = Auth::user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('status', 'Akun Anda telah dihapus.');
}

}
