<?php

namespace Modules\Core\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        return view('webmaster.profile.edit', compact('webmaster'));
    }

    public function update(Request $request)
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:webmasters,email,',
            'locale' => 'nullable|in:' . implode(',', array_keys(config('locales.locales', []))).$webmaster->id,
        ]);

        $webmaster->update($data);

        return back()->with('success', __('client.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $webmaster = \App\Helpers\AuthHelper::webmaster();

        if (!Hash::check($request->current_password, $webmaster->password)) {
            return back()->withErrors(['current_password' => __('client.invalid_current_password')]);
        }

        $webmaster->update(['password' => Hash::make($request->password)]);

        return back()->with('success', __('client.password_changed'));
    }
}
