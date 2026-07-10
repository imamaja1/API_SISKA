<?php

namespace App\Filament\Resources\Penggunas\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class PenggunaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nama_pengguna')
                    ->label('Nama Pengguna')
                    ->required()
                    ->maxLength(50),
                TextInput::make('nama_login')
                    ->label('Nama Login')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('sandi_pengguna')
                    ->label('Sandi Pengguna')
                    ->password()
                    ->required()
                    ->revealable()
                    ->mutateDehydratedStateUsing(fn ($state) => Hash::make($state))
                    ->maxLength(255),
                Select::make('id_role')
                    ->label('Role')
                    ->options(Role::pluck('nama_role', 'id_role'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
