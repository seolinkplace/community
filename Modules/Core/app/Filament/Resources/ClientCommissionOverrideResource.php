<?php
namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\ClientCommissionOverrideResource\Pages\ManageClientCommissionOverrides;
use App\Models\ClientCommissionOverride;
use Modules\Core\Models\UnifiedUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;

class ClientCommissionOverrideResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Фінанси'; }
    public static function getNavigationSort(): ?int { return 5; }

    protected static ?string $model = ClientCommissionOverride::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PercentBadge;
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'VIP Commission';

    public static function table(Table $table): Table
    {
        $formComponents = fn() => [
            Select::make('user_id')
                ->label('User (client or webmaster)')
                ->options(UnifiedUser::orderBy('email')->pluck('email', 'id'))
                ->searchable()
                ->required(),
            Select::make('role')
                ->label('Role')
                ->options(['client' => 'Client', 'webmaster' => 'Webmaster'])
                ->required(),
            TextInput::make('withdrawal_pct')
                ->label('Commission on withdrawal %')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->step(0.01)
                ->suffix('%')
                ->required(),
            Textarea::make('note')
                ->label('Note')
                ->rows(2),
        ];

        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn($state) => $state === 'webmaster' ? 'info' : 'success'),
                TextColumn::make('withdrawal_pct')
                    ->label('Commission %')
                    ->suffix('%')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('note')
                    ->label('Note')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->fillForm(fn($record) => $record->toArray())
                    ->form($formComponents())
                    ->action(fn($record, array $data) => $record->update($data)),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->delete()),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Add override')
                    ->icon('heroicon-o-plus')
                    ->form($formComponents())
                    ->action(fn(array $data) => ClientCommissionOverride::create(
                        array_merge($data, ['created_by' => auth()->id()])
                    )),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClientCommissionOverrides::route('/'),
        ];
    }
}
