<?php

namespace Modules\Core\Filament\Resources\PlatformRules\Pages;

use Modules\Core\Filament\Resources\PlatformRules\PlatformRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlatformRules extends ListRecords
{
    protected static string $resource = PlatformRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
