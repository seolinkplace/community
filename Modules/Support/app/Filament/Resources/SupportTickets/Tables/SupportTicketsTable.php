<?php
namespace Modules\Support\Filament\Resources\SupportTickets\Tables;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_reply_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),
                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->description(fn($record) => $record->role),
                TextColumn::make('subject')
                    ->label('Тема')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'open'        => 'success',
                        'in_progress' => 'info',
                        'resolved'    => 'gray',
                        'closed'      => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'open'        => 'Відкрито',
                        'in_progress' => 'В роботі',
                        'resolved'    => 'Вирішено',
                        'closed'      => 'Закрито',
                        default       => $state,
                    }),
                TextColumn::make('priority')
                    ->label('Пріоритет')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'high'   => 'danger',
                        'normal' => 'warning',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'high'   => 'Високий',
                        'normal' => 'Нормальний',
                        'low'    => 'Низький',
                        default  => $state,
                    }),
                TextColumn::make('assignedTo.name')
                    ->label('Призначено')
                    ->default('—'),
                TextColumn::make('last_reply_at')
                    ->label('Остання відповідь')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'open'        => 'Відкрито',
                        'in_progress' => 'В роботі',
                        'resolved'    => 'Вирішено',
                        'closed'      => 'Закрито',
                    ]),
                SelectFilter::make('priority')
                    ->label('Пріоритет')
                    ->options([
                        'high'   => 'Високий',
                        'normal' => 'Нормальний',
                        'low'    => 'Низький',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
