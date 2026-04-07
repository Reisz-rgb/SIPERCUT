<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'quota',
        'used',
        'remaining',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // STATIC HELPERS
    // =========================================================================

    /**
     * Ambil atau buat record saldo untuk user & tahun tertentu.
     * Default quota = 12 hari.
     */
    public static function getOrCreateBalance(int $userId, int $year): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['quota' => 12, 'used' => 0, 'remaining' => 12]
        );
    }

    /**
     * Hitung total saldo cuti yang tersedia (N + bonus N-1 + bonus N-2).
     * Bonus = floor(remaining / 2) dari tahun sebelumnya.
     *
     * @return array{
     *   n2: array{year: int, remaining: int, bonus: int},
     *   n1: array{year: int, remaining: int, bonus: int},
     *   n:  array{year: int, quota: int, used: int, remaining: int},
     *   total_available: int
     * }
     */
    public static function calculateTotalAvailable(int $userId, int $currentYear): array
    {
        $n2 = self::getOrCreateBalance($userId, $currentYear - 2);
        $n1 = self::getOrCreateBalance($userId, $currentYear - 1);
        $n  = self::getOrCreateBalance($userId, $currentYear);

        $bonusN2 = (int) floor($n2->remaining / 2);
        $bonusN1 = (int) floor($n1->remaining / 2);

        return [
            'n2' => ['year' => $currentYear - 2, 'remaining' => $n2->remaining, 'bonus' => $bonusN2],
            'n1' => ['year' => $currentYear - 1, 'remaining' => $n1->remaining, 'bonus' => $bonusN1],
            'n'  => ['year' => $currentYear, 'quota' => $n->quota, 'used' => $n->used, 'remaining' => $n->remaining],
            'total_available' => $n->remaining + $bonusN1 + $bonusN2,
        ];
    }

    /**
     * Recalculate saldo cuti tahunan (N, N-1, N-2) berdasarkan pengajuan yang APPROVED.
     *
     * Aturan deducting:
     *  1. Kurangi saldo tahun berjalan (N) terlebih dahulu
     *  2. Bila kurang, pakai bonus N-1 (1 hari bonus = 2 hari remaining N-1)
     *  3. Bila masih kurang, pakai bonus N-2 (1 hari bonus = 2 hari remaining N-2)
     *
     * Method ini dipanggil setiap kali admin mengubah status pengajuan agar saldo
     * selalu konsisten meski status bolak-balik (approved ↔ rejected).
     */
    public static function recalculateAnnualBalances(int $userId, int $year): array
    {
        // 1. Siapkan bucket saldo (buat bila belum ada)
        $n2 = self::getOrCreateBalance($userId, $year - 2);
        $n1 = self::getOrCreateBalance($userId, $year - 1);
        $n  = self::getOrCreateBalance($userId, $year);

        // 2. Reset saldo tahun berjalan ke quota penuh
        $n->used      = 0;
        $n->remaining = (int) $n->quota;
        $n->save();

        // 3. Reset N-1 dan N-2 berdasarkan pemakaian di tahun masing-masing
        foreach ([[$n2, $year - 2], [$n1, $year - 1]] as [$balance, $y]) {
            $ownUsed = self::sumApprovedDuration($userId, $y);

            $balance->used      = $ownUsed;
            $balance->remaining = max(0, (int) $balance->quota - $ownUsed);
            $balance->save();
        }

        // 4. Proses pengajuan tahun berjalan secara kronologis
        $requests = self::getApprovedRequests($userId, $year);

        foreach ($requests as $req) {
            $due = (int) $req->duration;
            if ($due <= 0) continue;

            // Deduct dari N
            if ($n->remaining >= $due) {
                $n->used      += $due;
                $n->remaining -= $due;
                $n->save();
                continue;
            }

            // Habiskan sisa N terlebih dahulu
            $due = self::deductFrom($n, $due);

            // Deduct bonus dari N-1
            if ($due > 0) {
                $due = self::deductBonus($n1, $due);
            }

            // Deduct bonus dari N-2
            if ($due > 0) {
                self::deductBonus($n2, $due);
            }

            // Bila $due masih > 0, saldo tidak cukup — seharusnya tertahan di validasi store()
        }

        return self::calculateTotalAvailable($userId, $year);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private static function sumApprovedDuration(int $userId, int $year): int
    {
        return (int) DB::table('leave_requests')
            ->where('user_id', $userId)
            ->where('jenis_cuti', 'Cuti Tahunan') 
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('start_date', $year)
            ->sum('duration');
    }

    private static function getApprovedRequests(int $userId, int $year)
    {
        return DB::table('leave_requests')
            ->select(['id', 'duration', 'start_date', 'created_at'])
            ->where('user_id', $userId)
            ->where('jenis_cuti', 'Cuti Tahunan') 
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Kurangi saldo langsung (1:1) dari bucket.
     * Mengembalikan sisa $due yang belum terpenuhi.
     */
    private static function deductFrom(self $balance, int $due): int
    {
        if ($balance->remaining <= 0) {
            return $due;
        }

        $take              = min($balance->remaining, $due);
        $balance->used      += $take;
        $balance->remaining -= $take;
        $balance->save();

        return $due - $take;
    }

    /**
     * Kurangi saldo via mekanisme bonus (1 hari bonus = 2 hari remaining).
     * Mengembalikan sisa $due yang belum terpenuhi.
     */
    private static function deductBonus(self $balance, int $due): int
    {
        if ($balance->remaining < 2) {
            return $due;
        }

        $availableBonus    = intdiv((int) $balance->remaining, 2);
        $useBonus          = min($availableBonus, $due);
        $balance->used      += $useBonus * 2;
        $balance->remaining -= $useBonus * 2;
        $balance->save();

        return $due - $useBonus;
    }
}