<?php
namespace Modules\Support\Filament\Resources\SupportTickets\Pages;

use Modules\Support\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resolve')
                ->label('Вирішено')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => !in_array($this->record->status, ['resolved', 'closed']))
                ->requiresConfirmation()
                ->modalHeading('Закрити тікет як вирішений?')
                ->action(function () {
                    $this->record->update(['status' => 'resolved']);
                    Notification::make()->title('Тікет позначено як вирішений')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('close')
                ->label('Закрити')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => !in_array($this->record->status, ['closed']))
                ->requiresConfirmation()
                ->modalHeading('Закрити тікет?')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->title('Тікет закрито')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            EditAction::make()->label('Змінити'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->messages()
            ->whereNotIn('sender_role', ['admin', 'moderator'])
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
