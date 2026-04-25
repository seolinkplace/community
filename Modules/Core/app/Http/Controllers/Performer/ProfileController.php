<?php
namespace Modules\Core\Http\Controllers\Performer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth('unified')->user();
        return view('performer.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth('unified')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($data);

        return back()->with('success', __('client.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth('unified')->user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('client.wrong_password')]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', __('client.password_updated'));
    }
}
