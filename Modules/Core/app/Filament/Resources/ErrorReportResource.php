<?php

namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\ErrorReportResource\Pages;
use App\Models\ErrorReport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ErrorReportResource extends Resource
{
    protected static ?string $model = ErrorReport::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-bug-ant';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ErrorReport::where('status', 'new')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getModelLabel(): string
    {
        return 'Error Report';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Error Reports';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('exception_class')
                    ->label('Exception')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->tooltip(fn($state) => $state)
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->tooltip(fn($state) => $state)
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
                    ->tooltip(fn($state) => $state),
                TextColumn::make('user_id')
                    ->label('User')
                    ->formatStateUsing(fn($state, $record) => $state ? "{$record->user_type}:{$state}" : '—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'new'      => 'danger',
                        'seen'     => 'warning',
                        'resolved' => 'success',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new'      => 'New',
                        'seen'     => 'Seen',
                        'resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(ErrorReport $record) => class_basename($record->exception_class))
                    ->modalContent(fn(ErrorReport $record) => view('filament.error-report-detail', ['record' => $record]))
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->before(function (ErrorReport $record) {
                        if ($record->status === 'new') {
                            $record->update(['status' => 'seen']);
                        }
                    }),
                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(ErrorReport $record) => $record->update(['status' => 'resolved']))
                    ->visible(fn(ErrorReport $record) => $record->status !== 'resolved'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(ErrorReport $record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorReports::route('/'),
        ];
    }
}
