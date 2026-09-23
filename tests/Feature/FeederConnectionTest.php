<?php

use App\Models\FeederCredential;
use App\Models\User;
use App\Services\FeederService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('renders the feeder credentials page with a test connection action', function () {
    Schema::create('users', function ($table) {
        $table->id();
    });

    $user = new class extends User implements FilamentUser
    {
        public function canAccessPanel(Panel $panel): bool
        {
            return true;
        }
    };
    $user->name = 'Test';
    $user->email = 'test-feeder@example.com';
    $user->password = bcrypt('password');
    $user->save();

    FeederCredential::create(['key_name' => '_feeder_config', 'key_value' => Crypt::encryptString('singleton')]);
    foreach ([
        'feeder_url' => 'http://127.0.0.1',
        'feeder_port' => '8080',
        'feeder_username' => 'user',
        'feeder_password' => 'secret',
        'feeder_endpoint' => 'ws/live2.php',
    ] as $k => $v) {
        FeederCredential::create(['key_name' => $k, 'key_value' => Crypt::encryptString($v)]);
    }

    $response = $this->actingAs($user)->get('/sa_api/feeder-credentials');

    $response->assertStatus(200);
    $response->assertSee('Test Koneksi');
});

it('does not throw when a credential row holds invalid ciphertext', function () {
    Schema::create('users', function ($table) {
        $table->id();
    });

    $user = new class extends User implements FilamentUser
    {
        public function canAccessPanel(Panel $panel): bool
        {
            return true;
        }
    };
    $user->name = 'Test';
    $user->email = 'test-feeder2@example.com';
    $user->password = bcrypt('password');
    $user->save();

    FeederCredential::create(['key_name' => '_feeder_config', 'key_value' => Crypt::encryptString('singleton')]);
    FeederCredential::create(['key_name' => 'feeder_url', 'key_value' => 'http://127.0.0.1']);
    FeederCredential::create(['key_name' => 'feeder_username', 'key_value' => 'plaintext-user']);

    $response = $this->actingAs($user)->get('/sa_api/feeder-credentials');

    $response->assertStatus(200);
    $response->assertSee('harus diisi ulang');
});

it('reports connection failure without throwing', function () {
    config(['feeder.retry' => 0, 'feeder.timeout' => 2]);

    $result = (new FeederService([
        'feeder_url' => 'http://127.0.0.1',
        'feeder_port' => '1',
        'feeder_username' => 'x',
        'feeder_password' => 'y',
        'feeder_endpoint' => 'ws/live2.php',
    ]))->testConnection();

    expect($result['ok'])->toBeFalse()
        ->and($result['message'])->toBeString();
});
