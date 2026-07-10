<?php

namespace App\Filament\Resources\Penggunas\Pages;

use App\Filament\Actions\LoggableAction;
use App\Filament\Resources\Penggunas\PenggunaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenggunas extends ListRecords
{
    protected static string $resource = PenggunaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record, $data) {
                    LoggableAction::logCreate($record, $data, 'filament.action.pengguna.create');
                }),
        ];
    }
}
