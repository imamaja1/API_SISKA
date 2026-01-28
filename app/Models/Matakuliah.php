<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
    protected $table = 'matakuliah';

    protected $primaryKey = 'id_matakuliah';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $casts = [
        'id_matakuliah' => 'integer',
        'jenis' => 'integer',
        'sks_teori' => 'integer',
        'sks_praktek' => 'integer',
        'sks_praktikum' => 'integer',
        'kode_pengguna' => 'integer',
        'tanggal_terbuat' => 'datetime',
        'kode_kompetensi' => 'integer',
        'kode_program_studi' => 'integer',
        'param1' => 'integer',
        'block' => 'string',
    ];

    protected $fillable = [
        'kode_matakuliah',
        'nama_matakuliah',
        'jenis',
        'sks_teori',
        'sks_praktek',
        'sks_praktikum',
        'kode_pengguna',
        'tanggal_terbuat',
        'kode_kompetensi',
        'kode_program_studi',
        'param1',
        'block',
    ];

    protected $hidden = [
        'id_matakuliah',
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'kode_program_studi', 'kode_program_studi');
    }
}
