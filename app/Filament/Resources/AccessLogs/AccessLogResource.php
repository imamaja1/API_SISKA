<?php

namespace App\Filament\Resources\AccessLogs;

use App\Filament\Resources\AccessLogs\Pages\CreateAccessLog;
use App\Filament\Resources\AccessLogs\Pages\EditAccessLog;
use App\Filament\Resources\AccessLogs\Pages\ListAccessLogs;
use App\Filament\Resources\AccessLogs\Pages\ViewAccessLog;
use App\Filament\Resources\AccessLogs\Schemas\AccessLogForm;
use App\Filament\Resources\AccessLogs\Schemas\AccessLogInfolist;
use App\Filament\Resources\AccessLogs\Tables\AccessLogsTable;
use App\Models\AccessLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AccessLogResource extends Resource
{
    protected static ?string $model = AccessLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Log Admin';

    protected static string|UnitEnum|null $navigationGroup = 'Logs';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AccessLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccessLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessLogsTable::configure($table);
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
            'index' => ListAccessLogs::route('/'),
            // 'create' => CreateAccessLog::route('/create'),
            // 'view' => ViewAccessLog::route('/{record}'),
            // 'edit' => EditAccessLog::route('/{record}/edit'),
        ];
    }
}
