<?php
namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Models\UnifiedUser;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function redirectForLink()
    {
        $user = Auth::guard('unified')->user();
        if (!$user) return redirect()->route('unified.login');

        $token = Str::random(40);
        Cache::store('redis')->put('gl_link:' . $token, $user->id, 300);

        return Socialite::driver('google')
            ->stateless()
            ->with(['state' => $token])
            ->redirect();
    }

    public function unlink()
    {
        $user = Auth::guard('unified')->user();
        $user->update(['google_id' => null]);
        $route = $user->hasRole('webmaster') ? 'webmaster.profile.edit' : 'client.profile.edit';
        return redirect()->route($route)->with('success', __('auth.google_unlinked'));
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable $e) {
            Log::error('GOOGLE ERROR: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            return redirect()->route('unified.login')
                ->with('error', __('auth.google_error'));
        }

        $googleId = $googleUser->getId();
        $email    = $googleUser->getEmail();

        // Перевіряємо state — токен прив'язки
        $state = $request->get('state', '');
        $linkUserId = null;
        if (strlen($state) === 40) {
            $linkUserId = Cache::store('redis')->pull('gl_link:' . $state);
        }

        if ($linkUserId) {
            $user = UnifiedUser::find($linkUserId);
            if ($user) {
                $existing = UnifiedUser::where('google_id', $googleId)
                    ->where('id', '!=', $user->id)->first();
                if ($existing) {
                    $route = $user->hasRole('webmaster') ? 'webmaster.profile.edit' : 'client.profile.edit';
                    return redirect()->route($route)
                        ->with('error', __('auth.google_already_linked'));
                }
                $user->update(['google_id' => $googleId, 'google_email' => $email]);
                Auth::guard('unified')->login($user, true);
                $route = $user->hasRole('webmaster') ? 'webmaster.profile.edit' : 'client.profile.edit';
                return redirect()->route($route)
                    ->with('success', __('auth.google_linked'));
            }
        }

        // Звичайний логін/реєстрація
        $user = UnifiedUser::where('google_id', $googleId)->first();

        if (!$user) {
            $user = UnifiedUser::where('email', $email)->first();
            if ($user) {
                if ($user->google_id && $user->google_id !== $googleId) {
                    return redirect()->route('unified.login')
                        ->with('error', __('auth.google_email_conflict'));
                }
                $user->update(['google_id' => $googleId, 'google_email' => $email]);
            }
        }

        if (!$user) {
            if (!Setting::get('registration_enabled', true)) {
                return redirect()->route('unified.login')
                    ->with('error', __('auth.registration_closed'));
            }
            $user = UnifiedUser::create([
                'name'         => $googleUser->getName() ?? $email,
                'email'        => $email,
                'password'     => Hash::make(Str::random(32)),
                'google_id'    => $googleId,
                'google_email' => $email,
                'status'       => 'active',
            ]);
            Auth::guard('unified')->login($user, true);
            return redirect()->route('unified.google.role.select');
        }

        if ($user->status === 'banned') {
            return redirect()->route('unified.login')
                ->with('error', __('auth.account_banned'));
        }

        Auth::guard('unified')->login($user, true);
        return redirect()->route('unified.dashboard');
    }
}
