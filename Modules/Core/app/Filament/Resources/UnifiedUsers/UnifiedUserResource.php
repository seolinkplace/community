<?php
namespace Modules\Core\Filament\Resources\UnifiedUsers;

use Modules\Core\Filament\Resources\UnifiedUsers\Pages\CreateUnifiedUser;
use Modules\Core\Filament\Resources\UnifiedUsers\Pages\EditUnifiedUser;
use Modules\Core\Filament\Resources\UnifiedUsers\Pages\ListUnifiedUsers;
use Modules\Core\Filament\Resources\UnifiedUsers\Pages\ViewUnifiedUser;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\CampaignsRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\DirectPaymentsRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\SitesRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\WalletTransactionsRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\WebmasterTransactionsRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\RelationManagers\WithdrawalsRelationManager;
use Modules\Core\Filament\Resources\UnifiedUsers\Schemas\UnifiedUserForm as UnifiedUserFormSchema;
use Modules\Core\Filament\Resources\UnifiedUsers\Schemas\UnifiedUserInfolist as UnifiedUserInfolistSchema;
use Modules\Core\Models\UnifiedUser;
use App\Services\AffiliateService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UnifiedUserResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Користувачі'; }
    public static function getNavigationSort(): ?int { return 1; }

    protected static ?string $model = UnifiedUser::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;
    protected static ?string $navigationLabel = 'Користувачі';

    public static function form(Schema $schema): Schema
    {
        return UnifiedUserFormSchema::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UnifiedUserInfolistSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('name')->label('Ім\'я')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles_list')
                    ->label('Ролі')
                    ->getStateUsing(fn($record) => implode(', ', $record->activeRoles()) ?: '—')
                    ->badge(),
                TextColumn::make('status')->label('Статус')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'active'  => 'success',
                        'banned'  => 'danger',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),
                TextColumn::make('ref_code')
                    ->label('Реф. код')
                    ->getStateUsing(fn($record) => app(AffiliateService::class)->generateCode($record)),
                TextColumn::make('wallet.balance')
                    ->label('Баланс')
                    ->money('USD')
                    ->placeholder('—'),
                TextColumn::make('chat_banned_at')
                    ->label('Чат бан')
                    ->dateTime('d.m.Y')
                    ->placeholder('—'),
                TextColumn::make('created_at')->label('Реєстрація')->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active'  => 'Активний',
                        'banned'  => 'Заблокований',
                        'pending' => 'Очікує',
                    ]),
            ])
            ->actions([
                Action::make('add_role')
                    ->label('Додати роль')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Select::make('role')
                            ->label('Роль')
                            ->options([
                                'client'    => 'Клієнт',
                                'webmaster' => 'Вебмастер',
                                'performer' => 'Виконавець',
                            ])
                            ->required(),
                    ])
                    ->action(fn($record, array $data) => $record->addRole($data['role'])),

                Action::make('ban')
                    ->label('Заблокувати')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn($record) => $record->status !== 'banned')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'banned'])),

                Action::make('unban')
                    ->label('Розблокувати')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'banned')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'active'])),

                Action::make('ban_chat')
                    ->label('Бан чату')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('warning')
                    ->visible(fn($record) => $record->chat_banned_at === null)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['chat_banned_at' => now()])),

                Action::make('unban_chat')
                    ->label('Розбан чату')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->visible(fn($record) => $record->chat_banned_at !== null)
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['chat_banned_at' => null])),

                Action::make('reset_password')
                    ->label('Скинути пароль')
                    ->icon('heroicon-o-key')
                    ->color('gray')
                    ->form([
                        TextInput::make('password')
                            ->label('Новий пароль')
                            ->password()
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(fn($record, array $data) => $record->update([
                        'password' => bcrypt($data['password']),
                    ])),

                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SitesRelationManager::class,
            CampaignsRelationManager::class,
            WalletTransactionsRelationManager::class,
            WebmasterTransactionsRelationManager::class,
            WithdrawalsRelationManager::class,
            DirectPaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUnifiedUsers::route('/'),
            'create' => CreateUnifiedUser::route('/create'),
            'view'   => ViewUnifiedUser::route('/{record}'),
            'edit'   => EditUnifiedUser::route('/{record}/edit'),
        ];
    }
}
