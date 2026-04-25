<?php
namespace Modules\Core\Filament\Resources\CommissionSettings;

use Modules\Core\Filament\Resources\CommissionSettings\Pages\ListCommissionSettings;
use Modules\Core\Filament\Resources\CommissionSettings\Pages\ViewCommissionSetting;
use App\Models\CommissionSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionSettingResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Фінанси'; }
    public static function getNavigationSort(): ?int { return 4; }

    protected static ?string $model = CommissionSetting::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ReceiptPercent;
    protected static ?string $navigationLabel = 'Комісія системи';
    
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('commission_pct')->label('Комісія системи %')->suffix('%')->sortable(),
                TextColumn::make('webmaster_pct')->label('Вебмастер %')->suffix('%'),
                TextColumn::make('valid_from')->label('Діє з')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('note')->label('Примітка')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->label('Створено')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('valid_from', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('+ Нові налаштування')
                    ->form([
                        TextInput::make('commission_pct')
                            ->label('Комісія системи (%)')
                            ->numeric()->required()->default(30)
                            ->suffix('%')->minValue(0)->maxValue(100),
                        TextInput::make('webmaster_pct')
                            ->label('Вебмастер (%)')
                            ->numeric()->required()->default(70)
                            ->suffix('%')->minValue(0)->maxValue(100),
                        DateTimePicker::make('valid_from')
                            ->label('Діє з')
                            ->required()
                            ->default(now()),
                        Textarea::make('note')->label('Примітка')->nullable(),
                    ])
                    ->mutateFormDataUsing(fn($data) => array_merge($data, ['created_by' => auth()->id()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommissionSettings::route('/'),
            'view'  => ViewCommissionSetting::route('/{record}'),
        ];
    }
}
