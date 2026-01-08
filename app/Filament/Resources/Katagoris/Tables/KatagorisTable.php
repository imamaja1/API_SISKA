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
                        LoggableAction::logEdit($record, $data, 'filament.action.categorie.edit');
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        LoggableAction::logDelete($record, 'filament.action.categorie.delete');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            LoggableAction::logBulkDelete($records, 'filament.action.categorie.delete');
                        }),
                ]),
            ]);
    }
}
