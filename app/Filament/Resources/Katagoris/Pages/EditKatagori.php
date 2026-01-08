<?php

namespace App\Filament\Resources\Katagoris\Pages;

use App\Filament\Resources\Katagoris\KatagoriResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKatagori extends EditRecord
{
    protected static string $resource = KatagoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }
}
