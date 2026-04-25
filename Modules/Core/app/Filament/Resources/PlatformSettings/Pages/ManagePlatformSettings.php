<?php
namespace Modules\Core\Filament\Resources\PlatformSettings\Pages;

use Modules\Core\Filament\Resources\PlatformSettingResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePlatformSettings extends ManageRecords
{
    protected static string $resource = PlatformSettingResource::class;

    public static function getNavigationLabel(): string
    {
        return 'Налаштування платформи';
    }
}
