<?php

namespace Modules\Core\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\PlatformRule;
use App\Models\RuleComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RulesController extends Controller
{
    public function index(): View
    {
        $rules = PlatformRule::where('is_published', true)
            ->orderBy('sort_order')
            ->withCount('comments')
            ->get();

        return view('rules.index', compact('rules'));
    }

    public function show(PlatformRule $rule): View
    {
        abort_if(!$rule->is_published, 404);

        $comments = $rule->publishedComments()->get();

        return view('rules.show', compact('rule', 'comments'));
    }

    public function storeComment(Request $request, PlatformRule $rule): RedirectResponse
    {
        abort_if(!Auth::guard('unified')->check(), 403);
        abort_if(!$rule->is_published, 404);

        $validated = $request->validate([
            'body'      => ['required', 'string', 'min:3', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:rule_comments,id'],
        ]);

        // Verify parent belongs to this rule
        if (!empty($validated['parent_id'])) {
            $parent = RuleComment::find($validated['parent_id']);
            abort_if($parent->rule_id !== $rule->id, 422);
            // Allow only one level of nesting
            abort_if($parent->parent_id !== null, 422);
        }

        RuleComment::create([
            'rule_id'   => $rule->id,
            'user_id'   => Auth::guard('unified')->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'body'      => $validated['body'],
        ]);

        return redirect()
            ->route('rules.show', $rule->slug)
            ->with('success', __('rules.comment_posted'));
    }
}
