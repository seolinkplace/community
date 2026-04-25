<?php

namespace Modules\Support\Filament\Resources\ChatMessages\Pages;

use Modules\Support\Filament\Resources\ChatMessages\ChatMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatMessages extends ListRecords
{
    protected static string $resource = ChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
