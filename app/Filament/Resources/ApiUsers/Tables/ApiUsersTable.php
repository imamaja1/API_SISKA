<?php

namespace App\Filament\Resources\ApiUsers\Tables;

use App\Filament\Actions\LoggableAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiUsersTable
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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inactive' => 'gray',
                        'active' => 'success',
                    })
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'pdpt' => 'danger',
                        'prodi' => 'info',
                        'akademik' => 'warning'
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record, $data = null) {
                        // Log the edit action to api_access_logs
                        LoggableAction::logEdit($record, $data, 'filament.action.api_user.edit');
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        // Log the delete action to api_access_logs
                        LoggableAction::logDelete($record, 'filament.action.api_user.delete');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            // Log the bulk delete action to api_access_logs
                            LoggableAction::logBulkDelete($records, 'filament.action.api_user.delete');
                        }),
                ]),
            ]);
    }
}
