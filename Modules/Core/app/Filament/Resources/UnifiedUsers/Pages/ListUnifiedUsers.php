<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Pages;

use Modules\Core\Filament\Resources\UnifiedUsers\UnifiedUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnifiedUsers extends ListRecords
{
    protected static string $resource = UnifiedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
