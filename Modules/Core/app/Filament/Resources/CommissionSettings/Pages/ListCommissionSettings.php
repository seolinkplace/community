<?php

namespace Modules\Core\Filament\Resources\CommissionSettings\Pages;

use Modules\Core\Filament\Resources\CommissionSettings\CommissionSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommissionSettings extends ListRecords
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
