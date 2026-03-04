<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'user_id',
        'year',
        'quota',
        'used',
        'remaining',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil / buat saldo cuti untuk user+year.
     * Pastikan quota minimal 12.
     */
    public static function getOrCreateBalance(int $userId, int $year): self
    {
        $balance = self::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['quota' => 12, 'used' => 0, 'remaining' => 12]
        );

        // betulin kalau quota kosong/0
        if ((int) $balance->quota <= 0) {
            $balance->quota = 12;
            $balance->used = (int) ($balance->used ?? 0);
            $balance->remaining = max(0, 12 - (int) $balance->used);
            $balance->save();
        }

        return $balance;
    }

    /**
     * SYNC saldo tahun tertentu dari leave_requests yang APPROVED saja.
     * ✅ Mengikuti yang disetujui ADMIN
     * ✅ Tidak menghitung pending/rejected
     * ✅ Menghitung SEMUA jenis cuti (Cuti Besar, Tahunan, dll) karena kamu mau dashboard ikut total disetujui admin.
     *
     * Durasi aman:
     * - pakai duration kalau ada
     * - kalau NULL => DATEDIFF(end_date, start_date) + 1
     */
    public static function syncYearFromRequests(int $userId, int $year): void
    {
        $approvedStatus = defined(\App\Models\LeaveRequest::class . '::STATUS_APPROVED')
            ? \App\Models\LeaveRequest::STATUS_APPROVED
            : 'approved';

        $approvedDays = (int) \App\Models\LeaveRequest::where('user_id', $userId)
            ->where('status', $approvedStatus)
            ->whereYear('start_date', $year)
            ->sum(DB::raw('COALESCE(duration, DATEDIFF(end_date, start_date) + 1)'));

        $balance = self::getOrCreateBalance($userId, $year);

        $quota = (int) ($balance->quota ?? 12);
        $used  = max(0, $approvedDays);

        $balance->used = $used;
        $balance->remaining = max(0, $quota - $used);
        $balance->save();
    }

    /**
     * Sync N, N-1, N-2 sekaligus (biar Catatan Cuti & total tersedia konsisten).
     */
    public static function syncAllYears(int $userId, int $currentYear): void
    {
        self::syncYearFromRequests($userId, $currentYear);
        self::syncYearFromRequests($userId, $currentYear - 1);
        self::syncYearFromRequests($userId, $currentYear - 2);
    }

    /**
     * Hitung TOTAL TERSEDIA:
     * total_available = remaining(N) + bonus(N-1) + bonus(N-2)
     * bonus = floor(remaining/2)
     */
    public static function calculateTotalAvailable(?int $userId, int $currentYear): array
    {
        if (!$userId) {
            return [
                'n2' => ['year' => $currentYear - 2, 'remaining' => 12, 'bonus' => 6],
                'n1' => ['year' => $currentYear - 1, 'remaining' => 12, 'bonus' => 6],
                'n'  => ['year' => $currentYear, 'quota' => 12, 'used' => 0, 'remaining' => 12],
                'total_available' => 24,
            ];
        }

        $n2 = self::getOrCreateBalance($userId, $currentYear - 2);
        $n1 = self::getOrCreateBalance($userId, $currentYear - 1);
        $n  = self::getOrCreateBalance($userId, $currentYear);

        $bonusN2 = (int) floor(((int) $n2->remaining) / 2);
        $bonusN1 = (int) floor(((int) $n1->remaining) / 2);

        $totalAvailable = (int) $n->remaining + $bonusN2 + $bonusN1;

        return [
            'n2' => ['year' => $currentYear - 2, 'remaining' => (int) $n2->remaining, 'bonus' => $bonusN2],
            'n1' => ['year' => $currentYear - 1, 'remaining' => (int) $n1->remaining, 'bonus' => $bonusN1],
            'n'  => [
                'year' => $currentYear,
                'quota' => (int) $n->quota,
                'used' => (int) $n->used,
                'remaining' => (int) $n->remaining
            ],
            'total_available' => $totalAvailable,
        ];
    }
}