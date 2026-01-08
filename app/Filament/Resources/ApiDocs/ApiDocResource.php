<?php

namespace App\Filament\Resources\ApiDocs;

use App\Filament\Resources\ApiDocs\Pages\CreateApiDoc;
use App\Filament\Resources\ApiDocs\Pages\EditApiDoc;
use App\Filament\Resources\ApiDocs\Pages\ListApiDocs;
use App\Filament\Resources\ApiDocs\Pages\ViewApiDoc;
use App\Filament\Resources\ApiDocs\Schemas\ApiDocForm;
use App\Filament\Resources\ApiDocs\Schemas\ApiDocInfolist;
use App\Filament\Resources\ApiDocs\Tables\ApiDocsTable;
use App\Models\ApiDoc;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApiDocResource extends Resource
{
    protected static ?string $model = ApiDoc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Api Document';

    protected static string|UnitEnum|null $navigationGroup = 'Settings Documentation';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ApiDocForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApiDocInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiDocsTable::configure($table);
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
            'index' => ListApiDocs::route('/'),
            // 'create' => CreateApiDoc::route('/create'),
            // 'view' => ViewApiDoc::route('/{record}'),
            // 'edit' => EditApiDoc::route('/{record}/edit'),
        ];
    }
}
