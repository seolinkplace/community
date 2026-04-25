<?php
namespace Modules\Support\Filament\Resources\ContactRequests\Pages;
use Modules\Support\Filament\Resources\ContactRequests\ContactRequestResource;
use Filament\Resources\Pages\ListRecords;
class ListContactRequests extends ListRecords
{
    protected static string $resource = ContactRequestResource::class;
}
