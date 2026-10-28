<?php

use App\Models\Dosen;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::create('pengguna', function ($table) {
        $table->increments('kode_pengguna');
        $table->string('nama_pengguna')->nullable();
        $table->string('nama_login')->nullable();
        $table->string('sandi_pengguna')->nullable();
        $table->integer('id_role')->default(14);
        $table->timestamp('create_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    });

    Schema::create('program_studi', function ($table) {
        $table->increments('kode_program_studi');
        $table->string('nama_program_studi')->nullable();
        $table->string('singkatan_program_studi')->nullable();
        $table->timestamps();
    });

    Schema::create('dosen', function ($table) {
        $table->increments('kode_dosen');
        $table->string('nama_dosen')->nullable();
        $table->string('field_studi')->nullable();
        $table->string('alumni')->nullable();
        $table->string('nik')->nullable();
        $table->string('no_telp')->nullable();
        $table->string('status_dosen')->default('T');
        $table->integer('homebase')->nullable();
        $table->string('alamat_email')->nullable();
        $table->string('sandi_pengguna')->nullable();
        $table->string('status_login')->default('N');
        $table->string('aktif')->default('A');
        $table->string('signature')->nullable();
        $table->string('chatid')->nullable();
        $table->timestamps();
    });
});

it('can get and filter dosen list via get request', function () {
    $staff = Pengguna::create([
        'nama_pengguna' => 'Staf Akademik',
        'nama_login' => 'staf_akademik_get',
        'id_role' => 14,
    ]);

    Dosen::create([
        'nama_dosen' => 'Dr. Budi Santoso, M.Kom',
        'nik' => '320112345678',
        'homebase' => 2,
        'status_dosen' => 'A',
    ]);

    Dosen::create([
        'nama_dosen' => 'Siti Aminah, M.T',
        'nik' => '320998765432',
        'homebase' => 3,
        'status_dosen' => 'A',
    ]);

    // Test with query params filter
    $response = $this->actingAs($staff, 'auth_divisi_siska')
        ->getJson('/api/v1/divisi/dosen?nik=320112&nama=Budi');

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.nama_dosen', 'Dr. Budi Santoso, M.Kom');

    // Test get all without params
    $resAll = $this->actingAs($staff, 'auth_divisi_siska')
        ->getJson('/api/v1/divisi/dosen');

    $resAll->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(2, 'data.data');
});

it('can show dosen detail by encrypted kode_dosen in body', function () {
    $staff = Pengguna::create([
        'nama_pengguna' => 'Staf Akademik',
        'nama_login' => 'staf_akademik',
        'id_role' => 14,
    ]);

    $dosen = Dosen::create([
        'nama_dosen' => 'Dr. Budi Santoso, M.Kom',
        'nik' => '320112345678',
        'field_studi' => 'Cyber Security',
    ]);

    $encryptedKode = Crypt::encryptString((string) $dosen->kode_dosen);

    $response = $this->actingAs($staff, 'auth_divisi_siska')
        ->postJson('/api/v1/divisi/dosen-show', [
            'kode_dosen' => $encryptedKode,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.nama_dosen', 'Dr. Budi Santoso, M.Kom')
        ->assertJsonPath('data.field_studi', 'Cyber Security');
});

it('can update dosen data via request body without url parameters', function () {
    $staff = Pengguna::create([
        'nama_pengguna' => 'Staf Akademik',
        'nama_login' => 'staf_akademik',
        'id_role' => 14,
    ]);

    $dosen = Dosen::create([
        'nama_dosen' => 'Dr. Budi Santoso, M.Kom',
        'nik' => '320112345678',
        'field_studi' => 'Old Topic',
    ]);

    $encryptedKode = Crypt::encryptString((string) $dosen->kode_dosen);

    $response = $this->actingAs($staff, 'auth_divisi_siska')
        ->putJson('/api/v1/divisi/update-dosen', [
            'kode_dosen' => $encryptedKode,
            'field_studi' => 'Artificial Intelligence',
            'no_telp' => '08123456789',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.field_studi', 'Artificial Intelligence')
        ->assertJsonPath('data.no_telp', '08123456789');

    expect($dosen->fresh()->field_studi)->toBe('Artificial Intelligence');
});
