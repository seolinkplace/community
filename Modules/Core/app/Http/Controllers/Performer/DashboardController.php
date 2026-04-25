<?php
namespace Modules\Core\Http\Controllers\Performer;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCompletion;
use App\Models\WebmasterTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes

    public function index()
    {
        $user     = auth('unified')->user();
        $wallet   = $user->webmasterWallet;
        $cacheKey = "dashboard:performer:v2:{$user->id}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $wallet) {
            $completedTasks = TaskCompletion::where('performer_id', $user->id)->where('status', 'approved')->count();
            $pendingTasks   = TaskCompletion::where('performer_id', $user->id)->where('status', 'pending')->count();
            $rejectedTasks  = TaskCompletion::where('performer_id', $user->id)->where('status', 'rejected')->count();
            $availableTasks = Task::where('status', 'active')
                ->whereRaw('completions_count < max_completions')
                ->count();

            $earningsRaw = WebmasterTransaction::where('webmaster_wallet_id', $wallet?->id)
                ->where('type', 'earning')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(amount) as total'))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day');

            $earningsDays = $this->fillDays($earningsRaw, 30);

            return compact(
                'completedTasks', 'pendingTasks', 'rejectedTasks',
                'availableTasks', 'earningsDays'
            );
        });

        $balance       = (float)($wallet?->balance ?? 0);
        $totalEarned30 = round(array_sum($data['earningsDays']['values']), 2);
        $taskStatuses  = [
            'approved' => $data['completedTasks'],
            'pending'  => $data['pendingTasks'],
            'rejected' => $data['rejectedTasks'],
        ];

        return view('performer.dashboard', array_merge($data, compact(
            'user', 'wallet', 'balance', 'totalEarned30', 'taskStatuses'
        )));
    }

    private function fillDays(\Illuminate\Support\Collection $raw, int $days): array
    {
        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day      = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d.m');
            $values[] = round((float)($raw[$day] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
