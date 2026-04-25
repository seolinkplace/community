<?php

namespace Modules\Core\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $client = \App\Helpers\AuthHelper::client();
        return view('client.profile.edit', compact('client'));
    }

    public function update(Request $request)
    {
        $client = \App\Helpers\AuthHelper::client();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:clients,email,'.$client->id,
            'locale' => 'nullable|in:' . implode(',', array_keys(config('locales.locales', []))),
            'company_name' => 'nullable|string|max:255',
        ]);

        $client->update($data);
        return back()->with('success', __('client.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $client = \App\Helpers\AuthHelper::client();

        if (!Hash::check($request->current_password, $client->password)) {
            return back()->withErrors(['current_password' => __('client.invalid_current_password')]);
        }

        $client->update(['password' => Hash::make($request->password)]);
        return back()->with('success', __('client.password_changed'));
    }
}
