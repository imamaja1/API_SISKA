<?php

namespace App\Filament\Resources\ApiUsers\Pages;

use App\Filament\Actions\LoggableAction;
use App\Filament\Resources\ApiUsers\ApiUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiUsers extends ListRecords
{
    protected static string $resource = ApiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record, $data) {
                    // Log the create action to api_access_logs
                    LoggableAction::logCreate($record, $data, 'filament.action.api_user.create');
                }),
        ];
    }
}
