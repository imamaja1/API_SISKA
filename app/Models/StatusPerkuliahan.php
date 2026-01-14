<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusPerkuliahan extends Model
{
    protected $table = 'status_perkuliahan';

    protected $primaryKey = 'kode_status_perkuliahan';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kode_tahun_akademik',
        'semester',
        'nim',
        'status_perkuliahan',
        'pembayaran_spp',
        'pembayaran_sks',
        'pembayaran_lab',
        'pengumpulan_krs',
        'kode_pengguna',
    ];

    protected $casts = [
        'kode_status_perkuliahan' => 'integer',
        'kode_tahun_akademik' => 'integer',
        'semester' => 'string',
        'nim' => 'string',
        'status_perkuliahan' => 'string',
        'pembayaran_spp' => 'string',
        'pembayaran_sks' => 'string',
        'pembayaran_lab' => 'string',
        'pengumpulan_krs' => 'string',
        'kode_pengguna' => 'integer',
        'tanggal_terbuat' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'kode_tahun_akademik', 'kode_tahun_akademik');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kode_pengguna', 'id');
    }
}
