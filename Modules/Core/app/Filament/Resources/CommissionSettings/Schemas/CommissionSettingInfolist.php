<?php
namespace Modules\Core\Filament\Resources\CommissionSettings\Schemas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
class CommissionSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('webmaster_withdrawal_pct')->label('Вивід Вебмастер (%)')->numeric(),
                TextEntry::make('performer_withdrawal_pct')->label('Вивід Performer (%)')->numeric(),
                TextEntry::make('client_withdrawal_pct')->label('Вивід Клієнт (%)')->numeric(),
                TextEntry::make('min_withdrawal_amount')->label('Мін. сума виводу ($)')->numeric(),
                TextEntry::make('deposit_fee_pct')->label('Комісія депозит (%)')->numeric(),
                TextEntry::make('commission_pct')->label('Загальна комісія legacy (%)')->numeric(),
                TextEntry::make('valid_from')->label('Діє з')->dateTime(),
                TextEntry::make('created_by')->label('Створив')->numeric(),
                TextEntry::make('note')->label('Нотатка')->placeholder('-')->columnSpanFull(),
                TextEntry::make('created_at')->label('Створено')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->label('Оновлено')->dateTime()->placeholder('-'),
            ]);
    }
}
