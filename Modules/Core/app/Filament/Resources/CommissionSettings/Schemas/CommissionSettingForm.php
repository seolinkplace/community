<?php
namespace Modules\Core\Filament\Resources\CommissionSettings\Schemas;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
class CommissionSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('webmaster_withdrawal_pct')
                    ->label('Комісія при виводі — Вебмастер (%)')
                    ->helperText('Відсоток який утримується з вебмастера при виводі.')
                    ->required()->numeric()->minValue(0)->maxValue(100)->default(30.0)->suffix('%'),
                TextInput::make('performer_withdrawal_pct')
                    ->label('Комісія при виводі — Performer (%)')
                    ->helperText('Відсоток який утримується з performer при виводі.')
                    ->required()->numeric()->minValue(0)->maxValue(100)->default(30.0)->suffix('%'),
                TextInput::make('client_withdrawal_pct')
                    ->label('Комісія при виводі — Клієнт (%)')
                    ->helperText('Відсоток при виводі невитраченого депозиту клієнтом.')
                    ->required()->numeric()->minValue(0)->maxValue(100)->default(15.0)->suffix('%'),
                TextInput::make('min_withdrawal_amount')
                    ->label('Мінімальна сума виводу ($)')
                    ->helperText('Однакова для всіх ролей.')
                    ->required()->numeric()->minValue(0)->default(10.0)->prefix('$'),
                TextInput::make('commission_pct')
                    ->label('Загальна комісія (legacy, %)')
                    ->helperText('Старе поле — залишено для сумісності.')
                    ->required()->numeric()->minValue(0)->maxValue(100)->default(30.0)->suffix('%'),
                TextInput::make('webmaster_pct')
                    ->label('Вебмастру (інформаційно, %)')
                    ->helperText('Розраховується автоматично: 100 - комісія. Не редагується.')
                    ->numeric()->disabled()->default(70.0)->suffix('%'),
                TextInput::make('deposit_fee_pct')
                    ->label('Комісія при депозиті (%)')
                    ->helperText('Відсоток який утримується при поповненні балансу.')
                    ->required()->numeric()->minValue(0)->maxValue(100)->default(10.0)->suffix('%'),
                DateTimePicker::make('valid_from')
                    ->label('Діє з')->required(),
                TextInput::make('created_by')
                    ->label('Створив (user_id)')->required()->numeric(),
                Textarea::make('note')
                    ->label('Нотатка')->default(null)->columnSpanFull(),
            ]);
    }
}
