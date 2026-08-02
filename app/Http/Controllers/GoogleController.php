<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class GoogleController extends Controller
{
    public function login(Request $request)
    {
        $token = $request->query('token');

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $user = $accessToken->tokenable;
        auth()->login($user);

        return redirect('/dashboard');
    }
    
    public function redirect()
    {
        // pakai stateless() supaya token bisa lewat WebView tanpa session mismatch
        return Socialite::driver('google')
                        ->stateless()
                        ->with(['prompt' => 'select_account'])
                        ->redirect();
    }

    public function callback()
    {
        $guser = Socialite::driver('google')->stateless()->user();
    
        $user = User::firstOrCreate(
          ['email' => $guser->getEmail()],
          ['name' => $guser->getName(), 'password' => bcrypt(str()->random())]
        );
        Auth::login($user);
    
        // Kirim token atau flag lewat URL
        $token = $user->createToken('mobile')->plainTextToken;
    
        return redirect("dompetmyledger://callback?token={$token}");
    }    
}