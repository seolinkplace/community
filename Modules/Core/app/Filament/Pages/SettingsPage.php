<?php
namespace Modules\Core\Filament\Pages;
use App\Models\Setting;
use App\Models\EmailSetting;
use App\Models\EmailLog;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page
{
    protected string $view = 'filament.pages.settings';
    protected static ?string $title = 'Налаштування';
    protected static ?string $navigationLabel = 'Налаштування';
    protected static ?int $navigationSort = 99;
    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-cog-6-tooth'; }
    public static function getNavigationGroup(): ?string { return 'Система'; }

    public static function getNavigationBadge(): ?string
    {
        $failed = EmailLog::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        return $failed > 0 ? (string)$failed : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'danger'; }

    public function toggleRegistration(): void
    {
        $current = Setting::get('registration_enabled', true);
        Setting::set('registration_enabled', !$current);
        Notification::make()->title($current ? 'Реєстрацію закрито' : 'Реєстрацію відкрито')->success()->send();
    }

    public function toggleFirstPost(): void
    {
        $current = Setting::get('first_post_required_global', true);
        Setting::set('first_post_required_global', !$current);
        Notification::make()->title($current ? 'Вимогу першої статті вимкнено' : 'Вимогу першої статті увімкнено')->success()->send();
    }

    public function toggleEmail(string $key): void
    {
        $setting = EmailSetting::find($key);
        if ($setting) {
            $setting->update(['enabled' => !$setting->enabled]);
            Notification::make()->title('Збережено')->success()->send();
        }
    }

    protected function getViewData(): array
    {
        $today = now()->toDateString();
        $month = now()->format('Y-m');

        $statsToday = EmailLog::selectRaw('type, status, COUNT(*) as cnt')
            ->whereDate('created_at', $today)
            ->groupBy('type', 'status')
            ->get();

        $statsMonth = EmailLog::selectRaw('type, status, COUNT(*) as cnt')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->groupBy('type', 'status')
            ->get();

        return [
            'emailSettings' => EmailSetting::all(),
            'statsToday'    => $statsToday,
            'statsMonth'    => $statsMonth,
            'failedRecent'  => EmailLog::where('status', 'failed')->latest()->limit(5)->get(),
        ];
    }
}
