<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kurikulum';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'kode_kurikulum';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Disable Laravel timestamps (table uses a different timestamp column).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kode_nama_kurikulum',
        'kode_matakuliah',
        'semester',
        'kode_pengguna',
        'tanggal_terbuat',
        'id_matakuliah',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'kode_kurikulum' => 'integer',
        'kode_nama_kurikulum' => 'integer',
        'kode_pengguna' => 'integer',
        'id_matakuliah' => 'integer',
        'tanggal_terbuat' => 'datetime',
    ];

    public function matakuliah()
    {
        return $this->hasOne(Matakuliah::class, 'id_matakuliah', 'id_matakuliah')
            ->select('id_matakuliah', 'nama_matakuliah', 'sks_teori', 'sks_praktek', 'sks_praktikum');
    }

    public function nilai()
    {
        return $this->hasMany(KRSDetail::class, 'id_matakuliah', 'id_matakuliah')
            ->join('krs', 'krs_detail.kode_krs', '=', 'krs.kode_krs')
            ->join('khs_detail', 'krs_detail.kode_krs_detail', '=', 'khs_detail.kode_krs_detail')
            ->join('sistem_penilaian_detail as spd', function ($join) {
                $join->on('khs_detail.nilai_akhir', '>=', 'spd.nilai_minimum')
                    ->on('khs_detail.nilai_akhir', '<=', 'spd.nilai_maksimum')
                    ->where('spd.kode_sistem_penilaian', 1);
            })
            ->select(
                'krs_detail.id_matakuliah',
                'krs.nim',
                'krs.semester',
                'khs_detail.nilai_akhir',
                'spd.grade',
                'spd.bobot_nilai'
            )
            ->orderBy('krs.semester', 'desc');
    }
}
