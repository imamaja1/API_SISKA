<?php

namespace App\Filament\Resources\ApiUsers\Pages;

use App\Filament\Resources\ApiUsers\ApiUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApiUser extends EditRecord
{
    protected static string $resource = ApiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
