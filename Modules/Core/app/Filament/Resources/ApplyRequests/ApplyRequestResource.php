<?php
namespace Modules\Core\Filament\Resources\ApplyRequests;
use App\Models\ApplyRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class ApplyRequestResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $model = ApplyRequest::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::InboxStack;
    protected static ?string $navigationLabel = 'Заявки';
        public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getModelLabel(): string { return 'Заявка'; }
    public static function getPluralModelLabel(): string { return 'Заявки з лендінгу'; }
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string)$count : null;
    }
    public static function getNavigationBadgeColor(): ?string { return 'info'; }
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('name')->label("Ім'я")->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->copyable(),
                TextColumn::make('role')->label('Роль')->badge()
                    ->color(fn(string $state) => match($state) {
                        'webmaster' => 'success', 'client' => 'info', 'both' => 'warning', default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match($state) {
                        'webmaster' => 'Вебмайстер', 'client' => 'Клієнт', 'both' => 'Обидва', default => $state,
                    }),
                TextColumn::make('site')->label('Сайт')->placeholder('—'),
                TextColumn::make('message')->label('Коментар')->limit(60)->placeholder('—'),
                TextColumn::make('locale')->label('Мова')->badge()->color('gray'),
            ])
            ->filters([
                SelectFilter::make('role')->label('Роль')->options([
                    'webmaster' => 'Вебмайстер', 'client' => 'Клієнт', 'both' => 'Обидва',
                ]),
            ])
            ->actions([DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListApplyRequests::route('/')];
    }
}
