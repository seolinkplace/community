<?php
namespace Modules\Support\Filament\Resources\SupportTickets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class MessagesRelationManager extends RelationManager
{
    protected static bool $isLazy = false;
    protected static string $relationship = 'messages';
    protected static ?string $title = 'Повідомлення';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('message')
                ->label('Повідомлення')
                ->required()
                ->maxLength(5000)
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                TextColumn::make('sender_role')
                    ->label('Від')
                    ->formatStateUsing(fn(string $state, $record): string =>
                        in_array($state, ['admin', 'moderator']) ? 'Support' : ($record->sender?->name ?? '—')
                    ),
                TextColumn::make('sender_role')
                    ->label('Роль')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'admin', 'moderator' => 'info',
                        'client'             => 'success',
                        'webmaster'          => 'warning',
                        default              => 'gray',
                    }),
                TextColumn::make('message')
                    ->label('Текст')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('is_read')
                    ->label('Прочитано юзером')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn(bool $state, $record): string =>
                        in_array($record->sender_role, ['admin', 'moderator'])
                            ? ($state ? 'Доставлено' : 'Не прочитано')
                            : ($state ? 'Так' : 'Ні')
                    ),
                TextColumn::make('created_at')
                    ->label('Час')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Відповісти')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sender_id']   = Auth::id();
                        $data['sender_role'] = 'admin';
                        $data['is_read']     = true;
                        return $data;
                    })
                    ->after(function () {
                        $this->getOwnerRecord()->update([
                            'status'        => 'in_progress',
                            'last_reply_at' => now(),
                        ]);
                    }),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
