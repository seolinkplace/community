<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Pages;

use Modules\Core\Filament\Resources\UnifiedUsers\UnifiedUserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUnifiedUser extends ViewRecord
{
    protected static string $resource = UnifiedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
