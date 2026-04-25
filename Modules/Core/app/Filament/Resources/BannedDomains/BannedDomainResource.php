<?php
namespace Modules\Core\Filament\Resources\BannedDomains;

use App\Models\BannedDomain;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BannedDomainResource extends Resource
{
    protected static ?string $model = BannedDomain::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::NoSymbol;
    protected static ?string $navigationLabel = 'Заблоковані домени';

    public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getModelLabel(): string { return 'Домен'; }
    public static function getPluralModelLabel(): string { return 'Заблоковані домени'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('domain')
                ->label('Домен')
                ->placeholder('example.com')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Select::make('category')
                ->label('Категорія')
                ->options([
                    'adult'      => 'Дорослий контент',
                    'spam'       => 'Спам',
                    'malware'    => 'Шкідливий',
                    'competitor' => 'Конкурент',
                    'phishing'   => 'Фішинг',
                    'other'      => 'Інше',
                ])
                ->required()
                ->default('other'),
            TextInput::make('reason')
                ->label('Причина')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->label('Домен')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Категорія')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'adult', 'malware', 'phishing' => 'danger',
                        'spam'  => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match($state) {
                        'adult'      => 'Дорослий',
                        'spam'       => 'Спам',
                        'malware'    => 'Шкідливий',
                        'competitor' => 'Конкурент',
                        'phishing'   => 'Фішинг',
                        default      => 'Інше',
                    }),
                TextColumn::make('reason')
                    ->label('Причина')
                    ->limit(50)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Додано')
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Категорія')
                    ->options([
                        'adult'      => 'Дорослий контент',
                        'spam'       => 'Спам',
                        'malware'    => 'Шкідливий',
                        'competitor' => 'Конкурент',
                        'phishing'   => 'Фішинг',
                        'other'      => 'Інше',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBannedDomains::route('/'),
            'create' => Pages\CreateBannedDomain::route('/create'),
            'edit'   => Pages\EditBannedDomain::route('/{record}/edit'),
        ];
    }
}
