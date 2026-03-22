<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'phone',
        'email',
        'password',
        'role',
        'gender',
        'pangkat_golongan',
        'bidang_unit',
        'jabatan',
        'pendidikan',
        'usia',
        'join_date',
        'status',
        'annual_leave_quota',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'join_date'         => 'date',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /** Relasi legacy ke tabel cuti (model lama). */
    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    /** Relasi ke tabel leave_requests (model baru). */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /** Relasi ke saldo cuti per tahun. */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return ($this->status ?? 'aktif') === 'aktif';
    }
}