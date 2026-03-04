<?php

namespace App\Filament\Superadmin\Resources\Users\Tables;

use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'info',
                        'superadmin' => 'danger',
                        default => 'default',
                    })
                    ->searchable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Suspended')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->date('M d, Y h:i A')
                    ->placeholder('Unverified')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->date('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleStatus')
                    ->label(fn ($record) => $record->is_active ? 'Suspend' : 'Activate')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->action(function ($record) {
                        $record->is_active = !$record->is_active;

                        if (!$record->is_active) {
                            Notification::make()
                                ->title('User Suspended')
                                ->body("The user {$record->name} has been suspended.")
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('User Activated')
                                ->body("The user {$record->name} has been activated.")
                                ->success()
                                ->send();
                        }
                            
                        $record->save();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
