<?php

namespace App\Filament\Resources\Penggunas\Tables;

use App\Filament\Actions\LoggableAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenggunasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pengguna')
                    ->label('Kode')
                    ->sortable(),
                TextColumn::make('nama_pengguna')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_login')
                    ->label('Login')
                    ->searchable(),
                TextColumn::make('role.nama_role')
                    ->label('Role')
                    ->badge()
                    ->sortable(),
                TextColumn::make('create_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record, $data = null) {
                        LoggableAction::logEdit($record, $data, 'filament.action.pengguna.edit');
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        LoggableAction::logDelete($record, 'filament.action.pengguna.delete');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            LoggableAction::logBulkDelete($records, 'filament.action.pengguna.delete');
                        }),
                ]),
            ]);
    }
}
