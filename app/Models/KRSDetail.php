<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KRSDetail extends Model
{
    protected $table = 'krs_detail';

    protected $primaryKey = 'kode_krs_detail';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kode_krs',
        'kode_matakuliah',
        'status',
        'id_matakuliah',
    ];

    protected $casts = [
        'kode_krs_detail' => 'integer',
        'kode_krs' => 'integer',
        'kode_matakuliah' => 'string',
        'status' => 'string',
        'id_matakuliah' => 'integer',
    ];

    public function krs(): BelongsTo
    {
        return $this->belongsTo(KRS::class, 'kode_krs', 'kode_krs');
    }

    public function khsDetail(): HasOne
    {
        return $this->hasOne(KHSDetail::class, 'kode_krs_detail', 'kode_krs_detail');
    }

    public function matakuliah(): HasOne
    {
        return $this->hasOne(Matakuliah::class, 'id_matakuliah', 'id_matakuliah')
            ->select('id_matakuliah', 'kode_matakuliah', 'nama_matakuliah', 'sks_teori', 'sks_praktek', 'sks_praktikum');
    }
}
