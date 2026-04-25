<?php
namespace Modules\Support\Filament\Resources\ChatMessages;

use Modules\Support\Filament\Resources\ChatMessages\Pages\ListChatMessages;
use Modules\Support\Filament\Resources\ChatMessages\Pages\ViewChatMessage;
use App\Models\ChatMessage;
use App\Models\Client;
use App\Models\Webmaster;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ChatMessageResource extends Resource
{
    public static function getNavigationGroup(): ?string { return 'Система'; }
    public static function getNavigationSort(): ?int { return 8; }

    protected static ?string $model = ChatMessage::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;
    protected static ?string $navigationLabel = 'Чати';
    
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('campaignLink.id')
                    ->label('Campaign Link')
                    ->url(fn($record) => '/admin/campaign-links/' . $record->campaign_link_id)
                    ->sortable(),
                TextColumn::make('sender_type')->label('Від')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'client'    => 'info',
                        'webmaster' => 'success',
                        'admin'     => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'client'    => 'Клієнт',
                        'webmaster' => 'Вебмастер',
                        'admin'     => 'Адмін',
                        default     => $state,
                    }),
                TextColumn::make('sender_name')->label('Ім\'я')
                    ->getStateUsing(fn($record) => $record->sender_name),
                TextColumn::make('body')->label('Повідомлення')->limit(60),
                TextColumn::make('read_at')->label('Прочитано')->dateTime('d.m.Y H:i')->placeholder('—'),
                TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('sender_type')
                    ->label('Тип відправника')
                    ->options([
                        'client'    => 'Клієнт',
                        'webmaster' => 'Вебмастер',
                        'admin'     => 'Адмін',
                    ]),
            ])
            ->headerActions([
                Action::make('toggle_chat')
                    ->label(fn() => Cache::get('chat_enabled', true) ? '🔴 Вимкнути чат' : '🟢 Увімкнути чат')
                    ->color(fn() => Cache::get('chat_enabled', true) ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function () {
                        $current = Cache::get('chat_enabled', true);
                        Cache::forever('chat_enabled', !$current);
                    }),
            ])
            ->actions([
                Action::make('reply')
                    ->label('Відповісти від адміна')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('warning')
                    ->form([
                        Textarea::make('body')
                            ->label('Повідомлення')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        ChatMessage::create([
                            'campaign_link_id' => $record->campaign_link_id,
                            'sender_type'      => 'admin',
                            'sender_id'        => 0,
                            'body'             => $data['body'],
                        ]);
                    }),

                Action::make('ban_sender')
                    ->label('Забанити')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn($record) => in_array($record->sender_type, ['client', 'webmaster']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->sender_type === 'client') {
                            Client::find($record->sender_id)?->update(['chat_banned_at' => now()]);
                        } else {
                            Webmaster::find($record->sender_id)?->update(['chat_banned_at' => now()]);
                        }
                    }),

                Action::make('unban_sender')
                    ->label('Розбанити')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function ($record) {
                        if ($record->sender_type === 'client') {
                            return Client::find($record->sender_id)?->chat_banned_at !== null;
                        }
                        if ($record->sender_type === 'webmaster') {
                            return Webmaster::find($record->sender_id)?->chat_banned_at !== null;
                        }
                        return false;
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->sender_type === 'client') {
                            Client::find($record->sender_id)?->update(['chat_banned_at' => null]);
                        } else {
                            Webmaster::find($record->sender_id)?->update(['chat_banned_at' => null]);
                        }
                    }),

                Action::make('delete')
                    ->label('Видалити')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatMessages::route('/'),
            'view'  => ViewChatMessage::route('/{record}'),
        ];
    }
}
