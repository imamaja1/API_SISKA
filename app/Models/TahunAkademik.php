<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tahun_akademik';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'kode_tahun_akademik';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tahun_akademik',
        'semester',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'status_kpat',
        'kode_pengguna',
        'kode_institusi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'kode_tahun_akademik' => 'integer',
        'semester' => 'string',
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'status' => 'string',
        'status_kpat' => 'string',
        'kode_pengguna' => 'integer',
        'tanggal_terbuat' => 'datetime',
        'kode_institusi' => 'integer',
    ];

    /**
     * Get the user who created this record.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'kode_pengguna', 'id');
    }

    /**
     * Scope a query to only include active academic years.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'A');
    }

    /**
     * Scope a query to only include inactive academic years.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'N');
    }

    /**
     * Scope a query to filter by semester (1 = Ganjil, 0 = Genap).
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Check if the academic year is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'A';
    }

    /**
     * Check if KPAT is active.
     */
    public function isKpatActive(): bool
    {
        return $this->status_kpat === 'A';
    }

    /**
     * Get semester name.
     */
    public function getSemesterNameAttribute(): string
    {
        return $this->semester === '1' ? 'Ganjil' : 'Genap';
    }

    public function statusPerkuliahan()
    {
        return $this->hasOne(StatusPerkuliahan::class, 'kode_tahun_akademik', 'kode_tahun_akademik')
            ->select('kode_tahun_akademik', 'semester', 'status_perkuliahan', 'pembayaran_spp', 'pembayaran_sks', 'pembayaran_lab', 'nim');
    }

    // cekkrs
    public function CekKRS()
    {
        return $this->hasMany(KRS::class, 'kode_tahun_akademik', 'kode_tahun_akademik');
    }
}
