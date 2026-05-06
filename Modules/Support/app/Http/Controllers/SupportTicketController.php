<?php

declare(strict_types=1);

namespace Modules\Support\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    private function currentUser()
    {
        return Auth::guard('unified')->user();
    }

    private function currentRole(): string
    {
        $user = $this->currentUser();
        $roles = $user->roles->pluck('role')->toArray();
        if (in_array('admin', $roles) || in_array('moderator', $roles)) return 'admin';
        if (in_array('webmaster', $roles)) return 'webmaster';
        return 'client';
    }

    public function index()
    {
        $user = $this->currentUser();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)->where('is_read', false);
            }])
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,normal,high',
        ]);

        $user = $this->currentUser();
        $role = $this->currentRole();

        $ticket = SupportTicket::create([
            'user_id'       => $user->id,
            'role'          => $role,
            'subject'       => $request->subject,
            'status'        => 'open',
            'priority'      => $request->priority ?? 'normal',
            'last_reply_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_id'   => $user->id,
            'sender_role' => $role,
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        return redirect()->route('unified.support.show', $ticket->id)
            ->with('success', __('support.ticket_created'));
    }

    public function show(int $id)
    {
        $user = $this->currentUser();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Позначаємо повідомлення адміна як прочитані
        $ticket->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $ticket->messages()->with('sender')->get();
        $isClosed = in_array($ticket->status, ['resolved', 'closed']);

        return view('support.show', compact('ticket', 'messages', 'isClosed'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $user = $this->currentUser();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->firstOrFail();

        SupportTicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_id'   => $user->id,
            'sender_role' => $this->currentRole(),
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        $ticket->update([
            'status'        => 'open',
            'last_reply_at' => now(),
        ]);

        return redirect()->route('unified.support.show', $ticket->id);
    }

    public function close(int $id)
    {
        $user = $this->currentUser();
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $ticket->update(['status' => 'closed']);

        return redirect()->route('unified.support.index')
            ->with('success', __('support.ticket_closed'));
    }
}
