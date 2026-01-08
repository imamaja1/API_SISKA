<?php

namespace App\Filament\Resources\Katagoris;

use App\Filament\Resources\Katagoris\Pages\CreateKatagori;
use App\Filament\Resources\Katagoris\Pages\EditKatagori;
use App\Filament\Resources\Katagoris\Pages\ListKatagoris;
use App\Filament\Resources\Katagoris\Schemas\KatagoriForm;
use App\Filament\Resources\Katagoris\Tables\KatagorisTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KatagoriResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kategori';

    protected static string|UnitEnum|null $navigationGroup = 'Settings Documentation';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return KatagoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KatagorisTable::configure($table);
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
            'index' => ListKatagoris::route('/'),
            // 'create' => CreateKatagori::route('/create'),
            // 'edit' => EditKatagori::route('/{record}/edit'),
        ];
    }
}
