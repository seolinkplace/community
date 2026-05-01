<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\WebmasterProfile;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\UnifiedUser;
use Modules\Core\Services\HcaptchaService;

class AuthController extends Controller
{
    public function __construct(private readonly HcaptchaService $hcaptcha) {}

    public function showLogin()
    {
        return view('unified.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email:rfc',
            'password' => 'required',
        ]);

        if (!$this->hcaptcha->verify($request->input('h-captcha-response'))) {
            return back()->withErrors(['email' => __('auth.captcha_failed')])->withInput();
        }
        if (!Auth::guard('unified')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('auth.invalid_credentials')])->withInput();
        }

        $user = Auth::guard('unified')->user();
        $roles = $user->activeRoles();

        // Редирект залежно від ролей
        if (count($roles) === 1) {
            return match($roles[0]) {
                'client'    => redirect()->route('unified.client.dashboard'),
                'webmaster' => redirect()->route('unified.webmaster.dashboard'),
                'performer' => redirect()->route('unified.performer.dashboard'),
                default     => redirect()->route('unified.dashboard'),
            };
        }

        // Кілька ролей — на загальний dashboard
        return redirect()->route('unified.dashboard');
    }

    public function showRegister()
    {
        $registrationEnabled = \App\Models\Setting::get('registration_enabled', true);
        return view('unified.auth.register', compact('registrationEnabled'));
    }

    public function register(Request $request)
    {
        if (!\App\Models\Setting::get('registration_enabled', true)) {
            return back()->withErrors(['email' => __('auth.registration_closed_error')]);
        }
        if (!(new HcaptchaService())->verify($request->input('h-captcha-response'))) {
            return back()->withErrors(['email' => __('auth.captcha_failed')])->withInput();
        }
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email:rfc',
            'password'     => 'required|min:8|confirmed',
            'role'         => 'required|in:client,webmaster,performer',
            'ref_code'     => 'nullable|string|max:20',
            'gdpr_consent' => 'accepted',
            'age_consent'  => 'accepted',
        ]);

        // Перевіряємо чи не був цей email видалений через GDPR
        $existing = UnifiedUser::where('email', $data['email'])->first();
        if ($existing) {
            if ($existing->gdpr_deleted) {
                // Новий акаунт — старі дані недоступні
                $existing->delete(); // hard delete старого
            } else {
                return back()->withErrors(['email' => __('validation.unique', ['attribute' => 'email'])]);
            }
        }

        try {
            $user = UnifiedUser::create([
                'name'            => $data['name'],
                'email'           => $data['email'],
                'password'        => Hash::make($data['password']),
                'status'          => 'active',
                'gdpr_consent_at' => now(),
                'gdpr_consent_ip' => $request->ip(),
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['email' => __('validation.unique', ['attribute' => 'email'])])->withInput();
        }

        $user->addRole($data['role']);

        // Створюємо профіль
        match($data['role']) {
            'client'    => ClientProfile::create(['user_id' => $user->id]),
            'webmaster' => WebmasterProfile::create(['user_id' => $user->id]),
            default     => null,
        };

        // Створюємо гаманець залежно від ролі
        if ($data['role'] === 'client') {
            \App\Models\Wallet::create(['user_id' => $user->id, 'balance' => 0, 'reserved' => 0]);
        } elseif ($data['role'] === 'webmaster' || $data['role'] === 'performer') {
            \App\Models\WebmasterWallet::create(['user_id' => $user->id, 'balance' => 0, 'pending' => 0]);
        }

        // Реєструємо реферала якщо є код
        if (!empty($data['ref_code'])) {
            app(\App\Services\AffiliateService::class)
                ->registerReferral($user, $data['ref_code']);
        }

        $user->sendEmailVerificationNotification();

        Auth::guard('unified')->login($user);

        return redirect()->route('unified.verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::guard('unified')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('unified.login');
    }

}
