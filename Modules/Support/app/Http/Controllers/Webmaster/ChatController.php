<?php
namespace Modules\Support\Http\Controllers\Webmaster;

use App\Http\Controllers\Controller;
use App\Models\CampaignLink;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function index()
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        $siteIds   = \App\Models\Site::where('webmaster_id', $webmaster->id)->pluck('id');

        $links = CampaignLink::whereIn('site_id', $siteIds)
            ->whereHas('messages')
            ->with(['site', 'campaign.client', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function($link) {
                $unread = ChatMessage::where('campaign_link_id', $link->id)
                    ->where('sender_type', 'client')
                    ->whereNull('read_at')
                    ->count();
                $link->unread_count = $unread;
                $link->last_message = $link->messages->first();
                return $link;
            })
            ->sortByDesc(fn($l) => $l->last_message?->created_at)
            ->values();

        return view('webmaster.chat.index', compact('links'));
    }

    public function show(CampaignLink $campaignLink)
    {
        $this->authorize($campaignLink);
        $this->checkChatEnabled();

        $messages = ChatMessage::where('campaign_link_id', $campaignLink->id)
            ->orderBy('created_at')
            ->get();

        // Позначаємо повідомлення клієнта як прочитані
        ChatMessage::where('campaign_link_id', $campaignLink->id)
            ->where('sender_type', 'client')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('webmaster.chat.show', compact('campaignLink', 'messages'));
    }

    public function send(Request $request, CampaignLink $campaignLink)
    {
        $this->authorize($campaignLink);
        $this->checkChatEnabled();

        $request->validate(['body' => 'required|string|max:2000']);

        $webmaster = \App\Helpers\AuthHelper::webmaster();

        if ($webmaster->chat_banned_at) {
            return response()->json(['error' => 'Чат заблоковано адміністратором.'], 403);
        }

        ChatMessage::create([
            'campaign_link_id' => $campaignLink->id,
            'sender_type'      => 'webmaster',
            'sender_id'        => $webmaster->id,
            'body'             => $request->body,
        ]);

        return response()->json(['ok' => true]);
    }

    public function poll(Request $request, CampaignLink $campaignLink)
    {
        $this->authorize($campaignLink);

        $since = $request->query('since', 0);

        $messages = ChatMessage::where('campaign_link_id', $campaignLink->id)
            ->where('id', '>', $since)
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'body'        => $m->body,
                'time'        => $m->created_at->format('d.m H:i'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    private function authorize(CampaignLink $link): void
    {
        $webmaster = \App\Helpers\AuthHelper::webmaster();
        $siteIds   = $webmaster->sites()->pluck('id');
        if (!$siteIds->contains($link->site_id)) {
            abort(403);
        }
    }

    private function checkChatEnabled(): void
    {
        if (!Cache::get('chat_enabled', true)) {
            abort(403, 'Чат тимчасово вимкнено.');
        }
    }
}
