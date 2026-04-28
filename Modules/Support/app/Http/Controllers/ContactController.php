<?php
namespace Modules\Support\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Services\HcaptchaService;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|max:255',
            'message'         => 'required|string|max:2000',
            'h-captcha-response' => 'required',
        ]);

        if (!(new HcaptchaService())->verify($request->input('h-captcha-response'))) {
            return back()->withErrors(['captcha' => __('contact.err_captcha')])->withInput();
        }

        $token = hash('sha256', $request->email . microtime() . Str::random(16));

        ContactRequest::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'message' => $request->message,
            'token'   => $token,
            'ip'      => $request->ip(),
        ]);

        return redirect()->route('contact.sent', $token);
    }

    public function sent(string $token)
    {
        $contact = ContactRequest::where('token', $token)->firstOrFail();
        return view('contact.sent', compact('contact'));
    }

    public function reply(string $token)
    {
        $contact = ContactRequest::where('token', $token)->firstOrFail();
        return view('contact.reply', compact('contact'));
    }
}
