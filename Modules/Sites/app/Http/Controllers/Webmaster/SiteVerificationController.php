<?php
namespace Modules\Sites\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SiteVerificationController extends Controller
{
    public function verify(Request $request, Site $site)
    {
        $this->authorizeSite($site);

        if ($site->verified_at) {
            return back()->with('success', __('client.site_already_verified'));
        }

        try {
            $url      = 'https://' . $site->domain . '/seolinkplace-verify.txt';
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return back()->with('error', __('client.verify_file_not_found'));
            }

            $content = trim($response->body());
            $expected = 'seolinkplace-verification=' . $site->verification_token;

            if (!str_contains($content, $expected)) {
                return back()->with('error', __('client.verify_token_mismatch'));
            }

            $site->update(['verified_at' => now()]);
            return back()->with('success', __('client.site_verified'));

        } catch (\Throwable $e) {
            return back()->with('error', __('client.verify_connection_error'));
        }
    }

    public function regenerateToken(Site $site)
    {
        $this->authorizeSite($site);
        $site->update([
            'verification_token' => bin2hex(random_bytes(24)),
            'verified_at'        => null,
        ]);
        return back()->with('success', __('client.verify_token_regenerated'));
    }

    private function authorizeSite(Site $site): void
    {
        $userId = \App\Helpers\AuthHelper::webmasterId();
        if ($site->user_id !== $userId && $site->webmaster_id !== $userId) {
            abort(403);
        }
    }
}
