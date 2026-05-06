<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function notice()
    {
        $user = auth('unified')->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('unified.dashboard');
        }

        return view('unified.auth.verify-email');
    }

    public function verify(Request $request)
    {
        $user = auth('unified')->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('unified.dashboard');
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), $request->route('hash'))) {
            abort(403);
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        $route = $user->onboarded_at ? 'unified.dashboard' : 'unified.onboarding';
        return redirect()->route($route)
            ->with('success', __('auth.verify_email_success'));
    }

    public function resend(Request $request)
    {
        $user = auth('unified')->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('unified.dashboard');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
