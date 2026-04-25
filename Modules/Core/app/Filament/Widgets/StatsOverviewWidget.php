<?php

namespace Modules\Core\Filament\Widgets;

use Modules\Core\Models\UnifiedUser;
use Modules\Sites\Models\Site;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalUsers   = UnifiedUser::where('gdpr_deleted', false)->count();
        $totalSites   = Site::where('status', 'active')->count();
        $needPostVerify = Site::where('first_post_required', true)
            ->where('first_post_published', false)
            ->count();

        return [
            Stat::make('Users', $totalUsers)
                ->description('Active accounts')
                ->color('primary'),
            Stat::make('Active sites', $totalSites)
                ->description($needPostVerify > 0 ? "{$needPostVerify} need post verification" : 'All verified')
                ->color($needPostVerify > 0 ? 'warning' : 'success'),
        ];
    }
}
