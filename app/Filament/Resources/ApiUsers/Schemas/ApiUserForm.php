<?php

namespace App\Filament\Resources\ApiUsers\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ApiUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->extraAttributes(['autocomplete' => 'new-password'])
                    ->suffixAction(
                        Action::make('generate_password')
                            ->icon('heroicon-o-arrow-path') // icon, bisa ganti
                            ->tooltip('Generate password')
                            ->action(function (callable $set) {
                                $generated = Str::random(12);
                                $set('password', $generated);
                            })
                    )
                    ->helperText('Klik ikon untuk generate password otomatis.'),
                Select::make('role')
                    ->required()
                    ->options([
                        'admin' => 'Admin',
                        'pdpt' => 'PDPT',
                        'prodi' => 'Prodi',
                        'akademik' => 'Akademik',
                    ])
                    ->default('admin'),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('Active'),
            ]);
    }
}
