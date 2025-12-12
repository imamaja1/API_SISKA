<?php

namespace App\Filament\Resources\ApiDocs\Pages;

use App\Filament\Resources\ApiDocs\ApiDocResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApiDoc extends ViewRecord
{
    protected static string $resource = ApiDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
