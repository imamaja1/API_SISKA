<?php

namespace App\Filament\Resources\FeederCredentials\Pages;

use App\Filament\Resources\FeederCredentials\FeederCredentialResource;
use App\Models\FeederCredential;
use App\Services\FeederService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class EditFeederConfig extends EditRecord
{
    protected static string $resource = FeederCredentialResource::class;

    protected static ?string $title = 'Konfigurasi Feeder PDDIKTI';

    public function mount(int|string|null $record = null): void
    {
        parent::mount($record ?? '_feeder_config');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test Koneksi')
                ->icon('heroicon-o-signal')
                ->color('gray')
                ->action(function (): void {
                    $data = $this->data ?? [];

                    $result = (new FeederService([
                        'feeder_url' => $data['feeder_url'] ?? '',
                        'feeder_port' => $data['feeder_port'] ?? '',
                        'feeder_username' => $data['feeder_username'] ?? '',
                        'feeder_password' => $data['feeder_password'] ?? '',
                        'feeder_endpoint' => $data['feeder_endpoint'] ?? '',
                    ]))->testConnection();

                    $notification = Notification::make()
                        ->title($result['ok'] ? 'Koneksi Feeder Berhasil' : 'Koneksi Feeder Gagal')
                        ->body($result['message']);

                    ($result['ok'] ? $notification->success() : $notification->danger())->send();
                }),
        ];
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

        $invalid = [];

        foreach ($keys as $key) {
            $encrypted = FeederCredential::where('key_name', $key)->value('key_value');

            if (! $encrypted) {
                $data[$key] = '';

                continue;
            }

            try {
                $data[$key] = Crypt::decryptString($encrypted);
            } catch (Throwable $exception) {
                report($exception);

                $data[$key] = '';
                $invalid[] = $key;
            }
        }

        if ($invalid !== []) {
            Notification::make()
                ->warning()
                ->title('Sebagian konfigurasi Feeder tidak valid')
                ->body('Nilai berikut gagal dibaca dan harus diisi ulang: '.implode(', ', $invalid).'.')
                ->send();
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
                        'key_value' => Crypt::encryptString((string) ($data[$key] ?? '')),
                        'description' => $description,
                    ]
                );
            }
        }

        return $this->getRecord()->attributesToArray();
    }
}
