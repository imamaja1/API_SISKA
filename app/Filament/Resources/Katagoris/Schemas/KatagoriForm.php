<?php

namespace App\Filament\Resources\Katagoris\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KatagoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Kategori Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->reactive()
                    ->columnSpanFull()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->nullable()
                    ->columnSpanFull()
                    ->rows(4),
            ]);
    }
}
