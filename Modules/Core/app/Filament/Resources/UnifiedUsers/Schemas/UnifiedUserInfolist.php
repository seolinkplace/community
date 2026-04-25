<?php

namespace Modules\Core\Filament\Resources\UnifiedUsers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnifiedUserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основне')
                    ->columns(3)
                    ->components([
                        TextEntry::make('name')
                            ->label('Ім\'я'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('locale')
                            ->label('Мова')
                            ->placeholder('—'),
                        TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->color(fn($state) => match($state) {
                                'active'  => 'success',
                                'banned'  => 'danger',
                                'pending' => 'warning',
                                default   => 'gray',
                            }),
                        TextEntry::make('email_verified_at')
                            ->label('Email підтверджено')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('Не підтверджено'),
                        IconEntry::make('is_trusted')
                            ->label('Довірений')
                            ->boolean(),
                        TextEntry::make('roles_list')
                            ->label('Ролі')
                            ->getStateUsing(fn($record) => implode(', ', $record->activeRoles()) ?: '—'),
                        TextEntry::make('created_at')
                            ->label('Реєстрація')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Оновлено')
                            ->dateTime('d.m.Y H:i'),
                    ]),

                Section::make('Безпека та обмеження')
                    ->columns(3)
                    ->collapsed()
                    ->components([
                        TextEntry::make('chat_banned_at')
                            ->label('Бан чату')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('banned_until')
                            ->label('Заблокований до')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('ban_reason')
                            ->label('Причина бану')
                            ->placeholder('—'),
                        TextEntry::make('warning_count')
                            ->label('Попереджень')
                            ->placeholder('0'),
                        TextEntry::make('gdpr_consent_at')
                            ->label('GDPR згода')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('—'),
                        IconEntry::make('gdpr_deleted')
                            ->label('GDPR видалено')
                            ->boolean(),
                        TextEntry::make('gdpr_deleted_at')
                            ->label('GDPR видалено о')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('gdpr_consent_ip')
                            ->label('IP згоди')
                            ->placeholder('—'),
                    ]),

                Section::make('Профіль клієнта')
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn($record) => $record->clientProfile !== null)
                    ->components([
                        TextEntry::make('clientProfile.company_name')
                            ->label('Компанія')
                            ->placeholder('—'),
                        TextEntry::make('clientProfile.plan')
                            ->label('План')
                            ->badge()
                            ->placeholder('free'),
                        TextEntry::make('clientProfile.trial_ends_at')
                            ->label('Тріал до')
                            ->dateTime('d.m.Y')
                            ->placeholder('—'),
                    ]),

                Section::make('Профіль вебмастера')
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn($record) => $record->webmasterProfile !== null)
                    ->components([
                        TextEntry::make('webmasterProfile.website')
                            ->label('Сайт')
                            ->placeholder('—'),
                        IconEntry::make('webmasterProfile.direct_payments_enabled')
                            ->label('Прямі платежі'),
                        TextEntry::make('webmasterProfile.usdt_address')
                            ->label('USDT адреса')
                            ->placeholder('—')
                            ->copyable(),
                    ]),

                Section::make('Гаманець клієнта')
                    ->columns(3)
                    ->collapsed()
                    ->visible(fn($record) => $record->wallet !== null)
                    ->components([
                        TextEntry::make('wallet.balance')
                            ->label('Баланс')
                            ->money('USD'),
                        TextEntry::make('wallet.reserved')
                            ->label('Зарезервовано')
                            ->money('USD'),
                        TextEntry::make('wallet_available')
                            ->label('Доступно')
                            ->getStateUsing(fn($record) => $record->wallet?->availableBalance())
                            ->money('USD'),
                    ]),

                Section::make('Гаманець вебмастера')
                    ->columns(4)
                    ->collapsed()
                    ->visible(fn($record) => $record->webmasterWallet !== null)
                    ->components([
                        TextEntry::make('webmasterWallet.balance')
                            ->label('Баланс')
                            ->money('USD'),
                        TextEntry::make('webmasterWallet.pending')
                            ->label('Очікує')
                            ->money('USD'),
                        TextEntry::make('webmaster_wallet_frozen')
                            ->label('Заморожено')
                            ->getStateUsing(fn($record) => $record->webmasterWallet?->frozenBalance())
                            ->money('USD'),
                        TextEntry::make('webmaster_wallet_available')
                            ->label('Доступно до виводу')
                            ->getStateUsing(fn($record) => $record->webmasterWallet?->availableForWithdrawal())
                            ->money('USD'),
                    ]),
            ]);
    }
}
