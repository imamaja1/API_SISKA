<?php

namespace App\Filament\Resources\FeederCredentials;

use App\Filament\Resources\FeederCredentials\Pages\EditFeederConfig;
use App\Filament\Resources\FeederCredentials\Schemas\FeederCredentialForm;
use App\Models\FeederCredential;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

use function Filament\Support\original_request;

class FeederCredentialResource extends Resource
{
    protected static ?string $model = FeederCredential::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'key_name';

    protected static string|UnitEnum|null $navigationGroup = 'Feeder';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return FeederCredentialForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditFeederConfig::route('/{record?}'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => original_request()->routeIs(static::getRouteBaseName().'.*'))
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return static::getUrl('edit', parameters: $parameters, isAbsolute: $isAbsolute, panel: $panel, tenant: $tenant, shouldGuessMissingParameters: $shouldGuessMissingParameters);
    }
}
