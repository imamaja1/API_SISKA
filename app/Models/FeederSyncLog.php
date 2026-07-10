<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeederSyncLog extends Model
{
    protected $table = 'feeder_sync_logs';

    protected $fillable = [
        'tipe',
        'tipe_sync',
        'referensi',
        'tahun_akademik',
        'semester',
        'kode_matakuliah',
        'kelas',
        'jumlah_data_feeder',
        'jumlah_data_siska',
        'jumlah_sync',
        'jumlah_gagal',
        'status',
        'synced_by',
        'log_detail',
    ];

    protected $casts = [
        'jumlah_data_feeder' => 'integer',
        'jumlah_data_siska' => 'integer',
        'jumlah_sync' => 'integer',
        'jumlah_gagal' => 'integer',
        'log_detail' => 'array',
    ];

    public function syncedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'synced_by');
    }

    public function getSummaryAttribute(): array
    {
        return [
            'total_data' => $this->jumlah_data_feeder,
            'updated' => $this->jumlah_sync,
            'created' => $this->log_detail ? collect($this->log_detail)->where('status', 'created')->count() : 0,
            'unchanged' => $this->log_detail ? collect($this->log_detail)->where('status', 'unchanged')->count() : 0,
            'failed' => $this->jumlah_gagal,
        ];
    }
}
