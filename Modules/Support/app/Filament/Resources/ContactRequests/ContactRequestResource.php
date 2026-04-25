<?php
namespace Modules\Support\Filament\Resources\ContactRequests;

use App\Models\ContactRequest;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use BackedEnum;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'Контактні запити';

    public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getNavigationSort(): ?int { return 9; }
    public static function getNavigationBadge(): ?string { $n = \App\Models\ContactRequest::whereNull('reply')->count(); return $n > 0 ? (string)$n : null; }
    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Імʼя')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('message')->label('Повідомлення')->limit(60),
                TextColumn::make('reply')->label('Відповідь')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->label('Дата')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactRequests::route('/'),
            'view'  => Pages\ViewContactRequest::route('/{record}'),
        ];
    }
}
