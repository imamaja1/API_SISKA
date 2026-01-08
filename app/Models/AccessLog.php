<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccessLog extends Model
{
    use HasFactory;

    protected $table = 'access_logs';

    protected $fillable = [
        'user_id',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The API user who performed the action (nullable).
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Polymorphic relation to the model that was affected.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return decoded request payload if stored as JSON.
     */
    public function getRequestPayloadAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        $decoded = json_decode($value, true);

        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
    }
}
