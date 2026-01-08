<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiAccessLog extends Model
{
    use HasFactory;

    protected $table = 'api_access_logs';

    // migration does not add default timestamps; use accessed_at instead
    public $timestamps = false;

    protected $fillable = [
        'api_user_id',
        'endpoint',
        'method',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'request_payload',
        'response_status',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    /**
     * The API user who made the request (nullable).
     */
    public function apiUser(): BelongsTo
    {
        return $this->belongsTo(ApiUser::class, 'api_user_id');
    }

    /**
     * Polymorphic relation to the model that was accessed.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
