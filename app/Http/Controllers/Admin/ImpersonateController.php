<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Webmaster;
use Modules\Core\Models\UnifiedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    public function asClient(Request $request, Client $client)
    {
        if (!Auth::guard('web')->check()) {
            abort(403);
        }

        // Client.id == UnifiedUser.id (legacy mapping)
        $user = UnifiedUser::find($client->id);

        if (!$user) {
            return back()->withErrors(['error' => 'UnifiedUser не знайдено для цього клієнта.']);
        }

        Session::put('impersonating_as', 'client');
        Session::put('impersonating_id', $user->id);
        Session::put('impersonator', Auth::guard('web')->id());

        Auth::guard('unified')->login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Ви увійшли як клієнт: ' . $user->name);
    }

    public function asWebmaster(Request $request, Webmaster $webmaster)
    {
        if (!Auth::guard('web')->check()) {
            abort(403);
        }

        // Webmaster.id == UnifiedUser.id (legacy mapping)
        $user = UnifiedUser::find($webmaster->id);

        if (!$user) {
            return back()->withErrors(['error' => 'UnifiedUser не знайдено для цього вебмастера.']);
        }

        Session::put('impersonating_as', 'webmaster');
        Session::put('impersonating_id', $user->id);
        Session::put('impersonator', Auth::guard('web')->id());

        Auth::guard('unified')->login($user);

        return redirect()->route('webmaster.dashboard')
            ->with('success', 'Ви увійшли як вебмастер: ' . $user->name);
    }

    public function leave(Request $request)
    {
        Session::forget('impersonating_as');
        Session::forget('impersonating_id');
        Session::forget('impersonator');

        Auth::guard('unified')->logout();

        return redirect('/admin')->with('success', 'Повернулись до адмінки.');
    }
}
