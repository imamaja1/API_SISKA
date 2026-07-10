<?php

namespace App\Filament\Resources\FeederCredentials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FeederCredentialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('feeder_url')
                    ->label('URL Feeder (IP Publik / Domain)')
                    ->helperText('Contoh: http://103.x.x.x')
                    ->required(),
                TextInput::make('feeder_port')
                    ->label('Port')
                    ->helperText('Contoh: 8080')
                    ->default('8080')
                    ->required(),
                TextInput::make('feeder_username')
                    ->label('Username')
                    ->required(),
                TextInput::make('feeder_password')
                    ->label('Password')
                    ->password()
                    ->required(),
                TextInput::make('feeder_endpoint')
                    ->label('Endpoint')
                    ->helperText('Default: ws/live2.php')
                    ->default('ws/live2.php'),
            ]);
    }
}
