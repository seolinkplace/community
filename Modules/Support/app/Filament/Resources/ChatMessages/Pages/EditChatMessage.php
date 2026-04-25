<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Pages;

use Modules\Support\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChatMessage extends EditRecord
{
    protected static string $resource = ChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
