<?php

namespace Modules\Core\Filament\Resources\CommissionSettings\Pages;

use Modules\Core\Filament\Resources\CommissionSettings\CommissionSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommissionSetting extends ViewRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
