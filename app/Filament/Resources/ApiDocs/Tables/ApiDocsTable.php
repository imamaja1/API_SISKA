<?php

namespace App\Filament\Resources\ApiDocs\Tables;

use App\Filament\Actions\LoggableAction;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record, $data = null) {
                        LoggableAction::logEdit($record, $data, 'filament.action.apidoc.edit');
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        LoggableAction::logDelete($record, 'filament.action.apidoc.delete');
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
