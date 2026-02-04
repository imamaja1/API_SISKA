<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Dosen
 *
 * Eloquent model for table `dosen`.
 *
 * - Primary key: `kode_dosen` (bigint)
 * - Timestamps: uses `created_at` and `updated_at`
 */
class Dosen extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    /** @var string */
    protected $table = 'dosen';

    /** @var string */
    protected $primaryKey = 'kode_dosen';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var array Mass assignable attributes */
    protected $fillable = [
        'nama_dosen',
        'field_studi',
        'alumni',
        'nik',
        'no_telp',
        'status_dosen',
        'homebase',
        'alamat_email',
        'sandi_pengguna',
        'status_login',
        'aktif',
        'signature',
        'chatid',
    ];

    /** @var array Hidden attributes for arrays / JSON */
    protected $hidden = [
        'sandi_pengguna',
    ];

    /** @var array Attribute casting */
    protected $casts = [
        'homebase' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** @var array Default attribute values */
    protected $attributes = [
        'status_dosen' => 'T',
        'status_login' => 'N',
        'aktif' => 'A',
        'chatid' => '',
    ];

    /* -----------------------------
     | Relations
     | ----------------------------- */

    /**
     * Homebase relation to `ProgramStudi`.
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'homebase', 'kode_program_studi');
    }

    /* -----------------------------
     | Accessors
     | ----------------------------- */

    /**
     * Get generated initials from `nama_dosen`.
     * Examples: "Ahmad Sulaiman" => "AS", "Dwi M. Putra" => "DMP"
     */
    public function getInitialsAttribute(): string
    {
        $name = trim((string) $this->nama_dosen);
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $name);
        $initials = '';

        foreach ($parts as $part) {
            $initials .= mb_substr($part, 0, 1);
            if (mb_strlen($initials) >= 3) {
                break;
            }
        }

        return Str::upper($initials);
    }

    /**
     * Convenience boolean accessor for `aktif`.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->aktif === 'A';
    }

    /* -----------------------------
     | Mutators
     | ----------------------------- */

    /**
     * Normalize email to lowercase when set.
     */
    public function setAlamatEmailAttribute($value): void
    {
        $this->attributes['alamat_email'] = $value ? Str::lower($value) : null;
    }

    /**
     * Normalize phone: store digits only.
     */
    public function setNoTelpAttribute($value): void
    {
        $clean = $value ? preg_replace('/\D+/', '', $value) : null;
        $this->attributes['no_telp'] = $clean;
    }

    /**
     * Hash `sandi_pengguna` (password-like) when set.
     * If value is already a hash, do not double-hash.
     */
    public function setSandiPenggunaAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['sandi_pengguna'] = null;

            return;
        }

        if (Hash::needsRehash($value)) {
            $this->attributes['sandi_pengguna'] = Hash::make($value);
        } else {
            $this->attributes['sandi_pengguna'] = $value;
        }
    }

    /* -----------------------------
     | Query scopes
     | ----------------------------- */

    /** Scope to only active records. */
    public function scopeActive($query)
    {
        return $query->where('aktif', 'A');
    }

    /** Scope to only records with an email. */
    public function scopeHasEmail($query)
    {
        return $query->whereNotNull('alamat_email')->where('alamat_email', '!=', '');
    }

    /* -----------------------------
     | Helpers / Initializers
     | ----------------------------- */

    /**
     * Initialize a new Dosen instance with sensible defaults.
     * Use this when creating a Dosen programmatically to ensure defaults are present.
     *
     * Example: Dosen::initialize(['nama_dosen' => 'A B']);
     */
    public static function initialize(array $attributes = []): self
    {
        $defaults = [
            'status_dosen' => 'T',
            'status_login' => 'N',
            'aktif' => 'A',
            'chatid' => '',
        ];

        return new self(array_merge($defaults, $attributes));
    }

    public function nama_prodi(): HasOne
    {
        return $this->hasOne(ProgramStudi::class, 'kode_program_studi', 'homebase');
    }
}
