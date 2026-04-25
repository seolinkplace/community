<?php
namespace Modules\Core\Filament\Resources\TenantTokenResource\Pages;

use Modules\Core\Filament\Resources\TenantTokenResource;
use Filament\Resources\Pages\ListRecords;

class ListTenantTokens extends ListRecords
{
    protected static string $resource = TenantTokenResource::class;
}
