<?php

namespace App\Filament\Resources\ApiUsers\Pages;

use App\Filament\Resources\ApiUsers\ApiUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApiUser extends CreateRecord
{
    protected static string $resource = ApiUserResource::class;
}
