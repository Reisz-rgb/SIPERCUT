<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'jabatan',
        'unit_kerja',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /** Hanya atasan yang masih aktif */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Filter berdasarkan unit kerja */
    public function scopeByUnit($query, string $unitKerja)
    {
        return $query->where('unit_kerja', $unitKerja);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Format: "NAMA — Jabatan (Unit Kerja)" untuk dropdown */
    public function getLabelAttribute(): string
    {
        return "{$this->nama} — {$this->jabatan}";
    }
}