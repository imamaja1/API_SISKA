<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NamaKurikulum extends Model
{
    /** @var string */
    protected $table = 'nama_kurikulum';

    /** @var string */
    protected $primaryKey = 'kode_nama_kurikulum';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var string */
    const CREATED_AT = null;

    /** @var array */
    protected $fillable = [
        'nama_kurikulum',
        'kode_program_studi',
        'kode_pengguna',
        'tanggal_terbuat',
        'angkatan1',
        'ekstensi1',
        'paket1',
        'semester_stup_grade1',
    ];

    /** @var array */
    protected $casts = [
        'kode_nama_kurikulum' => 'integer',
        'kode_program_studi' => 'integer',
        'kode_pengguna' => 'integer',
        'tanggal_terbuat' => 'datetime',
        'semester_stup_grade1' => 'integer',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'kode_program_studi', 'kode_program_studi');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'kode_pengguna', 'kode_pengguna');
    }

    public function kurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'kode_nama_kurikulum', 'kode_nama_kurikulum');
    }
}
