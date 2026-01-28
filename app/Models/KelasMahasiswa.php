<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasMahasiswa extends Model
{
    /** @var string */
    protected $table = 'kelas_mahasiswa';

    /** @var string */
    protected $primaryKey = 'kelas_mahasiswa_id';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'kode_krs_detail',
        'kelas_id',
    ];

    /** @var array */
    protected $casts = [
        'kelas_mahasiswa_id' => 'integer',
        'kode_krs_detail' => 'integer',
        'kelas_id' => 'integer',
    ];

    protected $hidden = [
        'kelas_mahasiswa_id',
        'kode_krs_detail',
        'kelas_id',
    ];

    /**
     * Relasi ke tabel kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }

    /**
     * Relasi ke tabel krs_detail
     */
    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KRSDetail::class, 'kode_krs_detail', 'kode_krs_detail');
    }

    public function mahasiswa()
    {
        return $this->hasOne(
            KRSDetail::class, 'kode_krs_detail', 'kode_krs_detail')
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join('mahasiswa', 'krs.nim', '=', 'mahasiswa.nim')
            ->select(
                'kode_krs_detail',
                'mahasiswa.nim',
                'nama_mahasiswa',
            );
    }

    public function matakuliah()
    {
        return $this->hasOne(
            KRSDetail::class, 'kode_krs_detail', 'kode_krs_detail')
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join('matakuliah', 'krs_detail.id_matakuliah', '=', 'matakuliah.id_matakuliah')
            ->select(
                'kode_krs_detail',
                'matakuliah.kode_matakuliah',
                'nama_matakuliah',
                'sks_teori',
                'sks_praktek',
                'sks_praktikum',
            );
    }

    public function nilai()
    {
        return $this->hasOne(KHSDetail::class, 'kode_krs_detail', 'kode_krs_detail')
            ->select(
                'kode_krs_detail',
                'nilai_harian',
                'nilai_uts',
                'nilai_uas',
                'nilai_akhir',
            );
    }
}
