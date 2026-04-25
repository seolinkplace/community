<?php
namespace Modules\Support\Filament\Resources\ContactRequests\Pages;
use Modules\Support\Filament\Resources\ContactRequests\ContactRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewContactRequest extends ViewRecord
{
    protected static string $resource = ContactRequestResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label('Імʼя'),
            TextEntry::make('email')->label('Email'),
            TextEntry::make('created_at')->label('Дата')->dateTime(),
            TextEntry::make('ip')->label('IP')->placeholder('-'),
            TextEntry::make('message')->label('Повідомлення')->columnSpanFull(),
            TextEntry::make('reply')->label('Відповідь')->placeholder('Ще не відповіли')->columnSpanFull(),
            TextEntry::make('replied_at')->label('Відповідь надана')->dateTime()->placeholder('-'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('reply')
                ->label('Відповісти')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->form([
                    Textarea::make('reply')
                        ->label('Відповідь')
                        ->required()
                        ->rows(6)
                        ->default(fn() => $this->record->reply),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'reply'      => $data['reply'],
                        'replied_at' => now(),
                    ]);
                    Notification::make()->title('Відповідь збережено')->success()->send();
                    $this->refreshFormData(['reply', 'replied_at']);
                }),
        ];
    }
}
