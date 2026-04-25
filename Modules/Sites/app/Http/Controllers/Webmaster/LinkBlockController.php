<?php
namespace Modules\Sites\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Modules\Core\Helpers\AuthHelper;

class LinkBlockController extends Controller
{
    public function update(Request $request, Site $site)
    {
        // Ensure site belongs to current webmaster
        abort_unless(
            $site->user_id === AuthHelper::webmaster()->id,
            403
        );

        $validated = $request->validate([
            'display_mode'       => 'required|in:plain,block',
            'delimiter'          => 'nullable|string|max:50',
            'link_css_class'     => 'nullable|string|max:15',
            'orientation'        => 'required|in:horizontal,vertical',
            'show_header'        => 'boolean',
            'show_url'           => 'boolean',
            'sign_text'          => 'nullable|string|max:1000',
            'block_width'        => 'nullable|string|max:10',
            'text_align'         => 'required|in:left,center,right',
            'header_color'       => 'required|string|max:7',
            'header_size'        => 'required|integer|min:8|max:32',
            'header_decoration'  => 'required|in:none,underline',
            'text_color'         => 'required|string|max:7',
            'text_size'          => 'required|integer|min:8|max:32',
            'url_color'          => 'required|string|max:7',
            'url_size'           => 'required|integer|min:8|max:32',
            'bg_color'           => 'required|string|max:7',
            'border_color'       => 'required|string|max:7',
            'border_width'       => 'required|integer|min:0|max:10',
            'border_radius'      => 'boolean',
            'css_prefix'         => 'nullable|string|max:50',
            'font_family'        => 'nullable|string|max:100',
        ]);

        $site->update(['link_block_settings' => $validated]);

        return back()->with('tab', 'link-block')->with('success_block', __('client.link_block_saved'));
    }
}
