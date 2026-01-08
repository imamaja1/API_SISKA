<?php

namespace App\Filament\Resources\AccessLogs\Pages;

use App\Filament\Resources\AccessLogs\AccessLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAccessLog extends ViewRecord
{
    protected static string $resource = AccessLogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
