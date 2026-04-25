<?php
namespace Modules\Core\Filament\Resources;

use App\Models\TenantToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;

class TenantTokenResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getNavigationSort(): ?int { return 4; }

    protected static ?string $model = TenantToken::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;
    protected static ?string $navigationLabel = 'WP Tokens';
    protected static string|\UnitEnum|null $navigationGroup = 'Sites';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.domain')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('token')
                    ->label('Token')
                    ->limit(20)
                    ->copyable(),
                IconColumn::make('wp_enabled')
                    ->label('WP Enabled')
                    ->boolean(),
                IconColumn::make('wp_disabled_by_admin')
                    ->label('Disabled by Admin')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state === 'active' ? 'success' : 'danger'),
                TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('wp_enabled')
                    ->label('WP Enabled')
                    ->options([1 => 'Yes', 0 => 'No']),
            ])
            ->actions([
                Action::make('disable_wp')
                    ->label('Disable WP')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->wp_enabled && !$record->wp_disabled_by_admin)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'wp_enabled'           => false,
                        'wp_disabled_by_admin' => true,
                    ])),
                Action::make('enable_wp')
                    ->label('Enable WP')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->wp_disabled_by_admin)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'wp_enabled'           => true,
                        'wp_disabled_by_admin' => false,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Core\Filament\Resources\TenantTokenResource\Pages\ListTenantTokens::route('/'),
        ];
    }
}
