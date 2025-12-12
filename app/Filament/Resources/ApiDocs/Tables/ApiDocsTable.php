<?php

namespace App\Filament\Resources\ApiDocs\Tables;

use App\Filament\Actions\LoggableAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiDocsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
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
                ViewAction::make()
                    ->after(function ($record) {
                        LoggableAction::logView($record, 'filament.action.apidoc.view');
                    }),
                EditAction::make()
                    ->after(function ($record, $data = null) {
                        LoggableAction::logEdit($record, $data, 'filament.action.apidoc.edit');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            LoggableAction::logBulkDelete($records, 'filament.action.apidoc.delete');
                        }),
                ]),
            ]);
    }
}
