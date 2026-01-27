<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Mahasiswa extends Model
{
    use HasApiTokens;

    protected $table = 'mahasiswa';

    protected $primaryKey = 'nim';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'sandi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'nim',
        'nik',
        'npm',
        'nisn',
        'nomor_pendaftaran',
        'nomor_pendaftaran_ulang',
        'program_studi_kode',
        'nama_mahasiswa',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kota',
        'propinsi',
        'telepon',
        'jenis_kelamin',
        'agama',
        'golongan_darah',
        'kewarganegaraan',
        'nama_instansi',
        'email',
        'nama_ayah',
        'agama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'agama_ibu',
        'pekerjaan_ibu',
        'alamat_orangtua',
        'kota_orangtua',
        'propinsi_orangtua',
        'telepon_orangtua',
        'foto',
        'status',
        'status_pendaftaran',
        'ta_lulus',
    ];

    public function nama_prodi(): HasOne
    {
        return $this->hasOne(ProgramStudi::class, 'kode_program_studi', 'program_studi_kode')->select('kode_program_studi', 'nama_program_studi');
    }
}
