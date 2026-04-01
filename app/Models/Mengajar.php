<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Mengajar
 *
 * Eloquent model for table `mengajar`.
 *
 * - Primary key: `mengajar_id` (int, auto increment)
 * - Timestamps: none
 */
class Mengajar extends Model
{
    /** @var string */
    protected $table = 'mengajar';

    /** @var string */
    protected $primaryKey = 'mengajar_id';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'kode_dosen',
        'kelas_id',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'kode_dosen', 'kode_dosen');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'kelas_id');
    }
}
