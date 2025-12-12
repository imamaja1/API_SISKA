<?php

namespace App\Filament\Resources\Katagoris\Pages;

use App\Filament\Actions\LoggableAction;
use App\Filament\Resources\Katagoris\KatagoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKatagoris extends ListRecords
{
    protected static string $resource = KatagoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record, $data) {
                    LoggableAction::logCreate($record, $data, 'filament.action.categorie.create');
                }),
        ];
    }
}
