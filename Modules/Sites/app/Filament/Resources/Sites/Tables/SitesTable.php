<?php

namespace Modules\Sites\Filament\Resources\Sites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unifiedUser.name')
                    ->label('Webmaster')
                    ->searchable(),
                TextColumn::make('domain')
                    ->searchable(),
                TextColumn::make('platform_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('platform_url')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('followers_count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('first_post_required')
                    ->boolean()
                    ->label('Post req.'),
                IconColumn::make('first_post_published')
                    ->boolean()
                    ->label('Post pub.'),
                TextColumn::make('first_post_url')
                    ->label('Post URL')
                    ->limit(40)
                    ->url(fn ($record) => $record->first_post_url)
                    ->openUrlInNewTab()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('niche')
                    ->searchable(),
                TextColumn::make('language')
                    ->searchable(),
                TextColumn::make('dr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('traffic')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('content_type')
                    ->badge(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('visibility')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Deleted')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('platform_type')
                    ->options([
                        'website'   => 'Website',
                        'facebook'  => 'Facebook',
                        'instagram' => 'Instagram',
                        'tiktok'    => 'TikTok',
                        'linkedin'  => 'LinkedIn',
                        'telegram'  => 'Telegram',
                        'youtube'   => 'YouTube',
                        'twitter'   => 'Twitter/X',
                    ]),
                TernaryFilter::make('first_post_published')
                    ->label('First post published'),
                SelectFilter::make('status')
                    ->options([
                        'active'    => 'Active',
                        'suspended' => 'Suspended',
                        'rejected'  => 'Rejected',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                RestoreAction::make()
                    ->visible(fn ($record) => $record->trashed()),
                Action::make('verify_post')
                    ->label('Verify post')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->first_post_required && !$record->first_post_published && $record->first_post_url)
                    ->requiresConfirmation()
                    ->modalHeading('Підтвердити публікацію')
                    ->modalDescription(fn ($record) => 'URL: ' . ($record->first_post_url ?? '—'))
                    ->modalSubmitActionLabel('Підтвердити')
                    ->action(fn ($record) => $record->update(['first_post_published' => true])),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
