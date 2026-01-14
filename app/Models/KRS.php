<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KRS extends Model
{
    protected $table = 'krs';

    protected $primaryKey = 'kode_krs';

    public $incrementing = true;

    protected $keyType = 'int';

    public const CREATED_AT = null;

    public const UPDATED_AT = 'date_created';

    protected $fillable = [
        'kode_tahun_akademik',
        'nim',
        'semester',
    ];

    protected $casts = [
        'kode_krs' => 'integer',
        'kode_tahun_akademik' => 'integer',
        'nim' => 'string',
        'semester' => 'string',
        'date_created' => 'datetime',
    ];

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'kode_tahun_akademik', 'kode_tahun_akademik');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KRSDetail::class, 'kode_krs', 'kode_krs');
    }
}
