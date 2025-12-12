<?php

namespace App\Filament\Resources\AccessLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AccessLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('users.name')
                    ->label('User Name'),
                TextEntry::make('ip_address')
                    ->label('IP Address'),
                TextEntry::make('model_type')
                    ->label('Model Type'),
                TextEntry::make('method')
                    ->label('HTTP Method'),
                TextEntry::make('endpoint')
                    ->label('Endpoint'),
                TextEntry::make('response_status')
                    ->label('Response Status'),
                TextEntry::make('request_payload')
                    ->label('Request Payload'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
            ]);
    }
}
