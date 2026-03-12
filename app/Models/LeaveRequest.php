<?php

namespace App\Models;

use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const JENIS_TAHUNAN = 'Cuti Tahunan';

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'jenis_cuti',
        'start_date',
        'end_date',
        'duration',
        'reason',
        'address',
        'phone',
        'emergency_phone',
        'emergency_relationship',
        'notes',
        'file_path',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    // =========================================================================
    // QUERY SCOPES
    // =========================================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isAnnualLeave(): bool
    {
        return $this->jenis_cuti === self::JENIS_TAHUNAN;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default               => 'Pending',
        };
    }
}