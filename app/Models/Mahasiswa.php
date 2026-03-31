<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mahasiswa extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

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

    public function krs()
    {
        return $this->hasMany(KRS::class, 'nim', 'nim');
    }

    public function krs_detail()
    {
        return $this->hasManyThrough(
            KRSDetail::class, KRS::class, 'nim', 'kode_krs', 'nim', 'kode_krs'
        )->join('khs_detail', 'krs_detail.kode_krs_detail', '=', 'khs_detail.kode_krs_detail')
            ->select('krs_detail.*', 'khs_detail.kode_khs_detail', 'khs_detail.nilai_akhir');
    }
}
