<?php

namespace App\Filament\Resources\ApiDocs\Pages;

use App\Filament\Resources\ApiDocs\ApiDocResource;
use Filament\Resources\Pages\EditRecord;

class EditApiDoc extends EditRecord
{
    protected static string $resource = ApiDocResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
