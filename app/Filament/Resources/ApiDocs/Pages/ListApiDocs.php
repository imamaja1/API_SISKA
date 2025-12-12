<?php

namespace App\Filament\Resources\ApiDocs\Pages;

use App\Filament\Actions\LoggableAction;
use App\Filament\Resources\ApiDocs\ApiDocResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiDocs extends ListRecords
{
    protected static string $resource = ApiDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record, $data) {
                    LoggableAction::logCreate($record, $data, 'filament.action.apidoc.create');
                }),
        ];
    }
}
