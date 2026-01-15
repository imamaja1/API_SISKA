<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SistemPenilaianDetail
 *
 * @property int $kode_sistem_penilaian_detail
 * @property int|null $kode_sistem_penilaian
 * @property float $nilai_minimum
 * @property float $nilai_maksimum
 * @property string $grade
 * @property float $bobot_nilai
 * @property string $kategori
 * @property string $keterangan
 */
class SistemPenilaianDetail extends Model
{
    /** @var string */
    protected $table = 'sistem_penilaian_detail';

    /** @var string */
    protected $primaryKey = 'kode_sistem_penilaian_detail';

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $keyType = 'int';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'kode_sistem_penilaian',
        'nilai_minimum',
        'nilai_maksimum',
        'grade',
        'bobot_nilai',
        'kategori',
        'keterangan',
    ];

    /** @var array */
    protected $casts = [
        'kode_sistem_penilaian_detail' => 'integer',
        'kode_sistem_penilaian' => 'integer',
        'nilai_minimum' => 'float',
        'nilai_maksimum' => 'float',
        'bobot_nilai' => 'float',
    ];

    // Kategori enum values
    public const KATEGORI_SEMPURNA = 'Sempurna';

    public const KATEGORI_BAIK = 'Baik';

    public const KATEGORI_CUKUP = 'Cukup';

    public const KATEGORI_KURANG = 'Kurang';

    // Keterangan enum values
    public const KETERANGAN_LULUS = 'Lulus';

    public const KETERANGAN_TIDAK_LULUS = 'Tidak Lulus';

    public const KETERANGAN_GAGAL = 'Gagal';

    /**
     * Return available kategori options.
     *
     * @return string[]
     */
    public static function getKategoriOptions(): array
    {
        return [
            self::KATEGORI_SEMPURNA,
            self::KATEGORI_BAIK,
            self::KATEGORI_CUKUP,
            self::KATEGORI_KURANG,
        ];
    }

    /**
     * Return available keterangan options.
     *
     * @return string[]
     */
    public static function getKeteranganOptions(): array
    {
        return [
            self::KETERANGAN_LULUS,
            self::KETERANGAN_TIDAK_LULUS,
            self::KETERANGAN_GAGAL,
        ];
    }

    /**
     * Belongs to SistemPenilaian (master table).
     */
    public function sistemPenilaian()
    {
        return $this->belongsTo(SistemPenilaian::class, 'kode_sistem_penilaian', 'kode_sistem_penilaian');
    }
}
