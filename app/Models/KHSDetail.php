<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KHSDetail extends Model
{
    protected $table = 'khs_detail';

    protected $primaryKey = 'kode_khs_detail';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kode_krs_detail',
        'nilai_harian',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'tidak_berhak',
    ];

    protected $casts = [
        'kode_khs_detail' => 'integer',
        'kode_krs_detail' => 'integer',
        'nilai_harian' => 'float',
        'nilai_uts' => 'float',
        'nilai_uas' => 'float',
        'nilai_akhir' => 'float',
        'tidak_berhak' => 'string',
    ];

    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KRSDetail::class, 'kode_krs_detail', 'kode_krs_detail');
    }
}
