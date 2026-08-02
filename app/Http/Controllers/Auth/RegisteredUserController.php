<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);

        return view('auth.register', compact('num1', 'num2'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Honeypot Trap Check: If invisible field is filled by bot, redirect silently
        if ($request->filled('website_url')) {
            return redirect()->route('login')->with('status', 'Pendaftaran berhasil dikirim!');
        }

        // 2. Temp-Mail / Disposable Email Blocker Check
        $emailDomain = strtolower(substr(strrchr($request->email, "@"), 1));
        $disposableDomains = [
            'mailinator.com', 'tempmail.com', 'guerrillamail.com', 'yopmail.com',
            '10minutemail.com', 'dispostable.com', 'trashmail.com', 'sharklasers.com',
            'getnada.com', 'maildrop.cc', 'tempail.com', 'mohmal.com', 'burnermail.io',
            'crazymailing.com', 'throwawaymail.com', 'mytemp.email', 'temp-mail.org'
        ];

        if (in_array($emailDomain, $disposableDomains)) {
            return back()->withInput()->withErrors([
                'email' => 'Pendaftaran dengan domain email sementara (temp-mail) tidak diperbolehkan. Gunakan email resmi.'
            ]);
        }

        // 3. Math Captcha Verification
        $expectedCaptcha = session('captcha_answer');
        if (!$expectedCaptcha || (int)$request->captcha !== (int)$expectedCaptcha) {
            return back()->withInput()->withErrors([
                'captcha' => 'Jawaban verifikasi keamanan (matematika) kurang tepat. Silakan coba lagi.'
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Clear captcha session after successful validation
        session()->forget('captcha_answer');

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
