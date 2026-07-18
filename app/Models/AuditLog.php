<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'event', 'description', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|null $auditable
     */
    public static function log(
        string $event,
        ?string $description = null,
        $auditable = null,
        array $extra = []
    ): static {
        return static::create([
            'user_id' => $extra['user_id'] ?? Auth::id(),
            'event' => $event,
            'description' => $description,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => $extra['old_values'] ?? null,
            'new_values' => $extra['new_values'] ?? null,
            'ip_address' => $extra['ip_address'] ?? request()->ip(),
            'user_agent' => $extra['user_agent'] ?? request()->userAgent(),
        ]);
    }
}
