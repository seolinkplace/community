<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Pages;

use Modules\Core\Filament\Resources\UnifiedUsers\UnifiedUserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUnifiedUser extends EditRecord
{
    protected static string $resource = UnifiedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
