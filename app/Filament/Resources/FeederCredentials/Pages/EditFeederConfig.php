<?php

namespace App\Filament\Resources\FeederCredentials\Pages;

use App\Filament\Resources\FeederCredentials\FeederCredentialResource;
use App\Models\FeederCredential;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EditFeederConfig extends EditRecord
{
    protected static string $resource = FeederCredentialResource::class;

    protected static ?string $title = 'Konfigurasi Feeder PDDIKTI';

    public function mount(int|string|null $record = null): void
    {
        parent::mount($record ?? '_feeder_config');
    }

    protected function resolveRecord(int|string $key): Model
    {
        return FeederCredential::firstOrCreate(
            ['key_name' => '_feeder_config'],
            [
                'key_value' => Crypt::encryptString('singleton'),
                'description' => 'Konfigurasi Feeder (unified)',
            ]
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $keys = ['feeder_url', 'feeder_port', 'feeder_username', 'feeder_password', 'feeder_endpoint'];
        foreach ($keys as $key) {
            $encrypted = FeederCredential::where('key_name', $key)->value('key_value');
            $data[$key] = $encrypted ? Crypt::decryptString($encrypted) : '';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $keys = [
            'feeder_url' => 'URL Feeder (IP publik/domain)',
            'feeder_port' => 'Port Feeder',
            'feeder_username' => 'Username Feeder',
            'feeder_password' => 'Password Feeder',
            'feeder_endpoint' => 'Endpoint Feeder',
        ];

        foreach ($keys as $key => $description) {
            if (array_key_exists($key, $data)) {
                FeederCredential::updateOrCreate(
                    ['key_name' => $key],
                    [
                        'key_value' => Crypt::encryptString($data[$key]),
                        'description' => $description,
                    ]
                );
            }
        }

        return $this->getRecord()->attributesToArray();
    }
}
