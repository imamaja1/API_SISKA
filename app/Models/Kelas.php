<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    /** @var string */
    protected $table = 'kelas';

    /** @var string */
    protected $primaryKey = 'kelas_id';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'nama_kelas_id',
        'semester',
        'kode_matakuliah',
        'kode_tahun_akademik',
        'kode_program_studi',
        'id_matakuliah',
        'status_nilai',
        'validasi_nilai',
        'validasi_dekan',
        'catatan_prodi',
        'catatan_dekan',
        'datecreate',
        'status_nilai_uts',
        'validasi_nilai_uts',
        'validasi_dekan_uts',
        'param_uts',
        'param_uas',
        'status_revisi_uts',
        'status_revisi_uas',
        'cek_uts',
        'cek_uas',
        'valid_uts',
        'valid_uas',
    ];

    protected $hidden = [
        'nama_kelas_id',
        'kode_dosen',
        'id_matakuliah',
        'kelas_id',
    ];

    /** @var array */
    protected $casts = [
        'kode_tahun_akademik' => 'integer',
        'id_matakuliah' => 'integer',
        'datecreate' => 'datetime',
    ];

    public function nama_kelas()
    {
        return $this->hasOne(NamaKelas::class, 'nama_kelas_id', 'nama_kelas_id');
    }

    public function nama_kelas_kedokteran()
    {
        return $this->hasOne(NamaKelas::class, 'nama_kelas_id', 'nama_kelas_id')->select('nama_kelas_id', 'nama_kelas_id as id', 'nama_kelas');
    }

    public function dosen()
    {
        return $this->hasMany(Mengajar::class, 'kelas_id', 'kelas_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(KelasMahasiswa::class, 'kelas_id', 'kelas_id')->select('kelas_id as k_id', 'kelas_id', 'kode_krs_detail as k_krs_detail', 'kelas_mahasiswa_id as k_mahasiswa_id');
    }

    public function matakuliah()
    {
        return $this->hasOne(Matakuliah::class, 'id_matakuliah', 'id_matakuliah')
            ->select(
                'id_matakuliah',
                'kode_matakuliah',
                'nama_matakuliah',
                'sks_teori',
                'sks_praktek',
                'sks_praktikum'
            );
    }
}
