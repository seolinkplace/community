<?php
namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;
use App\Models\UserAppeal;
use App\Models\UserBlock;
use Illuminate\Http\Request;

class UserAppealController extends Controller
{
    /**
     * Сторінка бану з формою апеляції.
     */
    public function banned(): \Illuminate\View\View
    {
        $user = auth('unified')->user();

        $pendingAppeal = UserAppeal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('unified.banned', compact('user', 'pendingAppeal'));
    }

    /**
     * Подати апеляцію на бан акаунта або взаємний блок.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $userId = auth('unified')->id();

        $request->validate([
            'appeal_type'  => 'required|in:account_ban,user_block',
            'reference_id' => 'nullable|integer',
            'message'      => 'required|string|min:20|max:2000',
        ]);

        // Перевіряємо чи немає вже pending апеляції цього типу
        $exists = UserAppeal::where('user_id', $userId)
            ->where('appeal_type', $request->appeal_type)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', __('common.appeal_already_pending'));
        }

        UserAppeal::create([
            'user_id'      => $userId,
            'appeal_type'  => $request->appeal_type,
            'reference_id' => $request->reference_id,
            'message'      => $request->message,
            'status'       => 'pending',
        ]);

        return back()->with('success', __('common.appeal_submitted'));
    }
}
