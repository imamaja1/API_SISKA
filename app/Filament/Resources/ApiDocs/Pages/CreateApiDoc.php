<?php

namespace App\Filament\Resources\ApiDocs\Pages;

use App\Filament\Resources\ApiDocs\ApiDocResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApiDoc extends CreateRecord
{
    protected static string $resource = ApiDocResource::class;
}
