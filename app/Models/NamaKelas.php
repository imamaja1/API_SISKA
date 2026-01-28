<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NamaKelas extends Model
{
    /** @var string */
    protected $table = 'nama_kelas';

    /** @var string */
    protected $primaryKey = 'nama_kelas_id';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'nama_kelas',
    ];

    /** @var array */
    protected $casts = [
        'nama_kelas_id' => 'integer',
        'nama_kelas' => 'string',
    ];

    protected $hidden = [
        'nama_kelas_id',
    ];
}
