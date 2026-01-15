<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KurikulumAngkatan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kurikulum_angkatan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'kode_kurikulum_angkatan';

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
     * Disable timestamps for this table.
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
        'angkatan',
        'ekstensi',
        'paket',
        'semester_stup_grade',
        'kode_nama_kurikulum',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'semester_stup_grade' => 'integer',
        'kode_nama_kurikulum' => 'integer',
    ];

    public function program_studi()
    {
        return $this->hasOne(ProgramStudi::class, 'kode_program_studi', 'kode_program_studi')
            ->select('kode_program_studi', 'nama_program_studi', 'singkatan_program_studi');
    }
}
