<?php

namespace Modules\Core\Filament\Resources\PlatformRules\Pages;

use Modules\Core\Filament\Resources\PlatformRules\PlatformRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlatformRule extends EditRecord
{
    protected static string $resource = PlatformRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
