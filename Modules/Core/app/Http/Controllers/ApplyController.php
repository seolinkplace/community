<?php
namespace Modules\Core\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Services\HcaptchaService;
class ApplyController extends Controller
{
    public function store(Request $request)
    {
        $token = $request->input('h-captcha-response');
        if (!(new HcaptchaService())->verify($token)) {
            return response()->json(['status' => 'error', 'message' => 'Captcha failed'], 422);
        }
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'role'    => 'required|in:webmaster,client,both',
            'site'    => 'nullable|max:255',
            'message' => 'nullable|string|max:2000',
        ]);
        DB::table('apply_requests')->insert([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'site'       => $data['site'] ?? null,
            'message'    => $data['message'] ?? null,
            'locale'     => 'uk',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['status' => 'ok']);
    }
}
