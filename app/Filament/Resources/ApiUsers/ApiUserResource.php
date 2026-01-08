<?php

namespace App\Filament\Resources\ApiUsers;

use App\Filament\Resources\ApiUsers\Pages\ListApiUsers;
use App\Filament\Resources\ApiUsers\Schemas\ApiUserForm;
use App\Filament\Resources\ApiUsers\Tables\ApiUsersTable;
use App\Models\ApiUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApiUserResource extends Resource
{
    protected static ?string $model = ApiUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'apiuser';

    protected static string|UnitEnum|null $navigationGroup = 'Settings User';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ApiUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiUsers::route('/'),
        ];
    }
}
