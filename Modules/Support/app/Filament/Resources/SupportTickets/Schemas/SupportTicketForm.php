<?php
namespace Modules\Support\Filament\Resources\SupportTickets\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Тема')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Статус')
                    ->options([
                        'open'        => 'Відкрито',
                        'in_progress' => 'В роботі',
                        'resolved'    => 'Вирішено',
                        'closed'      => 'Закрито',
                    ])
                    ->default('open')
                    ->required(),
                Select::make('priority')
                    ->label('Пріоритет')
                    ->options([
                        'low'    => 'Низький',
                        'normal' => 'Нормальний',
                        'high'   => 'Високий',
                    ])
                    ->default('normal')
                    ->required(),
                Select::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->nullable(),
            ]);
    }
}
