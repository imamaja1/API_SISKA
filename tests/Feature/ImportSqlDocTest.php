<?php

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the import sql page', function () {
    $user = new class extends User implements FilamentUser
    {
        public function canAccessPanel(Panel $panel): bool
        {
            return true;
        }
    };
    $user->name = 'Test';
    $user->email = 'test-import@example.com';
    $user->password = bcrypt('password');
    $user->save();

    $response = $this->actingAs($user)->get('/sa_api/import-sql');

    $response->assertStatus(200);
    $response->assertSee('File SQL');
    $response->assertSee('Import');
});