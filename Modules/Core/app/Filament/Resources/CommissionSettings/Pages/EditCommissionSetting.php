<?php

namespace Modules\Core\Filament\Resources\CommissionSettings\Pages;

use Modules\Core\Filament\Resources\CommissionSettings\CommissionSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommissionSetting extends EditRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
