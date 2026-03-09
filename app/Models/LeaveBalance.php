<?php

namespace App\Models;

use App\Models\LeaveRequest;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get or create balance for specific year
     */
    public static function getOrCreateBalance($userId, $year)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['quota' => 12, 'used' => 0, 'remaining' => 12]
        );
    }
    
    /**
     * Calculate total available leave including N-2 and N-1
     */
    public static function calculateTotalAvailable($userId, $currentYear)
    {
        if (!$userId) {
            return (object)['quota' => 12, 'used' => 0, 'remaining' => 12];
        }

        // Hapus blok firstOrCreate yang salah ini ↑↑↑

        $n2 = self::getOrCreateBalance($userId, $currentYear - 2);
        $n1 = self::getOrCreateBalance($userId, $currentYear - 1);
        $n  = self::getOrCreateBalance($userId, $currentYear);

        $bonusN2 = floor($n2->remaining / 2);
        $bonusN1 = floor($n1->remaining / 2);

        $totalAvailable = $n->remaining + $bonusN2 + $bonusN1;

        return [
            'n2' => ['year' => $currentYear - 2, 'remaining' => $n2->remaining, 'bonus' => $bonusN2],
            'n1' => ['year' => $currentYear - 1, 'remaining' => $n1->remaining, 'bonus' => $bonusN1],
            'n'  => ['year' => $currentYear, 'quota' => $n->quota, 'used' => $n->used, 'remaining' => $n->remaining],
            'total_available' => $totalAvailable,
        ];
    }


/**
 * Recalculate leave balances (N, N-1, N-2) based on APPROVED annual leave requests.
 * Rule used in this project:
 * - Use current year remaining first
 * - If not enough, use bonus from N-1 (1 bonus day consumes 2 remaining days)
 * - If still not enough, use bonus from N-2 (1 bonus day consumes 2 remaining days)
 *
 * This makes dashboard consistent even if admin changes status (approved <-> rejected).
 */
public static function recalculateAnnualBalances(int $userId, int $year): array
{
    $annualType = 'Cuti Tahunan';
    $approved   = LeaveRequest::STATUS_APPROVED;

    // Prepare balances (create if missing)
    $n2 = self::getOrCreateBalance($userId, $year - 2);
    $n1 = self::getOrCreateBalance($userId, $year - 1);
    $n  = self::getOrCreateBalance($userId, $year);

    // Reset current year bucket
    $n->used = 0;
    $n->remaining = (int) $n->quota;
    $n->save();

    // For N-1 and N-2: first apply their OWN annual leaves in those years
    foreach ([[$n2, $year - 2], [$n1, $year - 1]] as $pair) {
        [$bal, $y] = $pair;
        $ownUsed = (int) DB::table('leave_requests')
            ->where('user_id', $userId)
            ->where('jenis_cuti', $annualType)
            ->where('status', $approved)
            ->whereYear('start_date', $y)
            ->sum('duration');

        $bal->used = $ownUsed;
        $bal->remaining = max(0, (int)$bal->quota - $ownUsed);
        $bal->save();
    }

    // Now process current year annual leaves in chronological order
    $requests = DB::table('leave_requests')
        ->select(['id','duration','start_date','created_at'])
        ->where('user_id', $userId)
        ->where('jenis_cuti', $annualType)
        ->where('status', $approved)
        ->whereYear('start_date', $year)
        ->orderBy('start_date')
        ->orderBy('created_at')
        ->get();

    foreach ($requests as $req) {
        $duration = (int) $req->duration;
        if ($duration <= 0) continue;

        // Deduct from N first
        if ($n->remaining >= $duration) {
            $n->used += $duration;
            $n->remaining -= $duration;
            $n->save();
            continue;
        }

        // Take what remains in N
        $remainingToCover = $duration;
        if ($n->remaining > 0) {
            $useFromN = min($n->remaining, $remainingToCover);
            $n->used += $useFromN;
            $n->remaining -= $useFromN;
            $n->save();
            $remainingToCover -= $useFromN;
        }

        // Use bonus from N-1 (consume 2 days remaining per 1 day)
        if ($remainingToCover > 0 && $n1->remaining >= 2) {
            $bonusN1 = intdiv((int)$n1->remaining, 2);
            $useBonus = min($bonusN1, $remainingToCover);
            $n1->used += $useBonus * 2;
            $n1->remaining -= $useBonus * 2;
            $n1->save();
            $remainingToCover -= $useBonus;
        }

        // Use bonus from N-2
        if ($remainingToCover > 0 && $n2->remaining >= 2) {
            $bonusN2 = intdiv((int)$n2->remaining, 2);
            $useBonus = min($bonusN2, $remainingToCover);
            $n2->used += $useBonus * 2;
            $n2->remaining -= $useBonus * 2;
            $n2->save();
            $remainingToCover -= $useBonus;
        }

        // If still remainingToCover > 0, berarti saldo memang tidak cukup (harusnya ketahan di validasi)
    }

    return self::calculateTotalAvailable($userId, $year);
}

}
