<?php

namespace App\Filament\Resources\ApiUsers\Pages;

use App\Filament\Resources\ApiUsers\ApiUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiUsers extends ListRecords
{
    protected static string $resource = ApiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
