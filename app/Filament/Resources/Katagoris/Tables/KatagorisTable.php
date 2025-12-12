<?php

namespace App\Filament\Resources\Katagoris\Tables;

use App\Filament\Actions\LoggableAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KatagorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record, $data = null) {
                        // Log the edit action to api_access_logs
                        LoggableAction::logEdit($record, $data, 'filament.action.categorie.edit');
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        // Log the delete action to api_access_logs
                        LoggableAction::logDelete($record, 'filament.action.categorie.delete');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            // Log bulk delete action: create one log entry per deleted record
                            LoggableAction::logBulkDelete($records, 'filament.action.categorie.delete');
                        }),
                ]),
            ]);
    }
}
