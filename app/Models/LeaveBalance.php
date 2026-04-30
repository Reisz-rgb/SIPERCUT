<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveRequest;

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

    public static function getOrCreateBalance(int $userId, int $year): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['quota' => 12, 'used' => 0, 'remaining' => 12]
        );
    }

    public static function calculateTotalAvailable(int $userId, int $currentYear): array
    {
        $n2 = self::getOrCreateBalance($userId, $currentYear - 2);
        $n1 = self::getOrCreateBalance($userId, $currentYear - 1);
        $n  = self::getOrCreateBalance($userId, $currentYear);

        $bonusN2 = ($n2->used == 0) ? 6 : 0;
        $bonusN1 = ($n1->used == 0) ? 6 : 0;

        return [
            'n2' => ['year' => $currentYear - 2, 'used' => $n2->used, 'bonus' => $bonusN2],
            'n1' => ['year' => $currentYear - 1, 'used' => $n1->used, 'bonus' => $bonusN1],
            'n'  => [
                'year' => $currentYear, 
                'quota' => $n->quota, 
                'used' => $n->used, 
                'remaining' => $n->remaining
            ],
            'total_available' => $n->remaining + $bonusN1 + $bonusN2,
        ];
    }

    public static function recalculateAnnualBalances(int $userId, int $year): array
    {
        return DB::transaction(function () use ($userId, $year) {
            $n2 = self::getOrCreateBalance($userId, $year - 2);
            $n1 = self::getOrCreateBalance($userId, $year - 1);
            $n  = self::getOrCreateBalance($userId, $year);

            // 1. Reset data tahun berjalan
            $n->used = 0;
            $n->remaining = $n->quota;

            // 2. Refresh data pemakaian tahun N-1 dan N-2 dari database
            // Penting: used di sini menentukan apakah bonus tersedia atau tidak
            $n2->used = self::sumApprovedDuration($userId, $year - 2);
            $n1->used = self::sumApprovedDuration($userId, $year - 1);
            
            $n2->save();
            $n1->save();

            // 3. Hitung ketersediaan bonus berdasarkan used
            $bonusN1Available = ($n1->used == 0) ? 6 : 0;
            $bonusN2Available = ($n2->used == 0) ? 6 : 0;

            // 4. Proses semua request tahun ini
            $requests = self::getApprovedRequests($userId, $year);
            foreach ($requests as $req) {
                $due = (int) $req->duration;

                // A. Potong dari jatah utama N (12 hari)
                $takeFromN = min($n->remaining, $due);
                $n->used += $takeFromN;
                $n->remaining -= $takeFromN;
                $due -= $takeFromN;

                // B. Jika masih ada sisa, potong dari Bonus N-1
                if ($due > 0 && $bonusN1Available > 0) {
                    $takeFromB1 = min($bonusN1Available, $due);
                    $bonusN1Available -= $takeFromB1;
                    $due -= $takeFromB1;
                }

                // C. Jika masih ada sisa, potong dari Bonus N-2
                if ($due > 0 && $bonusN2Available > 0) {
                    $takeFromB2 = min($bonusN2Available, $due);
                    $bonusN2Available -= $takeFromB2;
                    $due -= $takeFromB2;
                }
            }

            $n->save();

            return self::calculateTotalAvailable($userId, $year);
        });
    }

    private static function sumApprovedDuration(int $userId, int $year): int
    {
        return (int) DB::table('leave_requests')
            ->where('user_id', $userId)
            ->where('jenis_cuti', 'Cuti Tahunan') 
            ->where('status', 'APPROVED') // Sesuaikan dengan string status Anda
            ->whereYear('start_date', $year)
            ->sum('duration');
    }

    private static function getApprovedRequests(int $userId, int $year): \Illuminate\Support\Collection
    {
        return LeaveRequest::query()
            ->select(['id', 'duration', 'start_date', 'created_at'])
            ->where('user_id', $userId)
            ->annualLeave()         
            ->approved()             
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->orderBy('created_at')
            ->get();
    }

    private static function deductFrom(self $balance, int $due): int
    {
        $take = min($balance->remaining, $due);
        $balance->used += $take;
        $balance->remaining -= $take;
        return $due - $take;
    }

    private static function deductBonus(self $balance, int $due): int
    {
        $availableBonus = (int) floor($balance->remaining / 2);
        $useBonus = min($availableBonus, $due);
        
        $balance->used += ($useBonus * 2);
        $balance->remaining -= ($useBonus * 2);
        
        return $due - $useBonus;
    }
}