<?php
namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\UserAppealResource\Pages\ListUserAppeals;
use Modules\Core\Filament\Resources\UserAppealResource\Pages\ViewUserAppeal;
use App\Models\UserAppeal;
use Modules\Core\Models\UnifiedUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;

class UserAppealResource extends Resource
{
    protected static ?string $model = UserAppeal::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Scale;
    protected static ?string $navigationLabel = 'Appeals';

    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationSort(): ?int { return 1; }

    public static function getNavigationBadge(): ?string
    {
        $count = UserAppeal::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                \Filament\Forms\Components\TextInput::make('user_name')
                    ->label('User')
                    ->disabled()
                    ->getStateUsing(fn($record) => $record?->user?->name . ' (' . $record?->user?->email . ')'),
                \Filament\Forms\Components\TextInput::make('appeal_type')->disabled(),
                Textarea::make('message')->disabled()->rows(4),
                Textarea::make('admin_note')->rows(3)->label('Admin note'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->width(60),
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('appeal_type')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'account_ban' => 'danger',
                        'user_block'  => 'warning',
                        default       => 'gray',
                    }),
                TextColumn::make('message')->limit(50)->label('Message'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
                SelectFilter::make('appeal_type')
                    ->options([
                        'account_ban' => 'Account ban',
                        'user_block'  => 'User block',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),

                // Схвалити апеляцію — зняти бан
                Action::make('approve')
                    ->label('Approve & unban')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Admin note (shown to user)')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        if ($record->appeal_type === 'account_ban') {
                            UnifiedUser::where('id', $record->user_id)->update([
                                'banned_until' => null,
                                'ban_reason'   => null,
                            ]);
                        }
                        $record->update([
                            'status'      => 'approved',
                            'admin_note'  => $data['admin_note'],
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                // Відхилити апеляцію
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Admin note (shown to user)')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_note'  => $data['admin_note'],
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                // Заблокувати скаржників після нечесної апеляції
                Action::make('ban_abuser')
                    ->label('Ban appellant')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Select::make('duration')
                            ->label('Ban duration')
                            ->options([
                                '1'  => '1 day',
                                '7'  => '7 days',
                                '30' => '30 days',
                                '0'  => 'Permanent',
                            ])
                            ->required(),
                        Textarea::make('ban_reason')
                            ->label('Reason shown to user')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        $bannedUntil = $data['duration'] === '0'
                            ? now()->addYears(100)
                            : now()->addDays((int) $data['duration']);
                        UnifiedUser::where('id', $record->user_id)->update([
                            'banned_until' => $bannedUntil,
                            'ban_reason'   => $data['ban_reason'],
                        ]);
                        $record->update([
                            'status'      => 'rejected',
                            'admin_note'  => 'Appellant banned for abuse: ' . $data['ban_reason'],
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),

                // Попередження апелянту
                Action::make('warn')
                    ->label('Issue warning')
                    ->icon('heroicon-o-exclamation-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Issue warning to this user?')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        UnifiedUser::where('id', $record->user_id)->increment('warning_count');
                        $record->update([
                            'status'      => 'rejected',
                            'admin_note'  => 'Warning issued.',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserAppeals::route('/'),
            'view'  => ViewUserAppeal::route('/{record}'),
        ];
    }
}
