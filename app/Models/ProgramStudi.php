<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';

    protected $primaryKey = 'kode_program_studi';

    protected $keyType = 'int';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'kode_program_studi' => 'integer',
        'id_jurusan' => 'integer',
        'id_jenjang' => 'integer',
        'kode_pengguna' => 'integer',
        'tanggal_terbuat' => 'datetime',
    ];

    protected $fillable = [
        'kode_program_studi',
        'id_jurusan',
        'id_jenjang',
        'nama_program_studi',
        'singkatan_program_studi',
        'kode_fakultas',
        'kode_prodi_univ',
        'kode_pengguna',
        'tanggal_terbuat',
        'kompetensi',
    ];
}
