<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        
        return self::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['quota' => 12, 'used' => 0, 'remaining' => 12]
        );

        $n2 = self::getOrCreateBalance($userId, $currentYear - 2);
        $n1 = self::getOrCreateBalance($userId, $currentYear - 1);
        $n = self::getOrCreateBalance($userId, $currentYear);
        
        // Setengah dari sisa N-2 dan N-1 ditambahkan ke N
        $bonusN2 = floor($n2->remaining / 2);
        $bonusN1 = floor($n1->remaining / 2);
        
        $totalAvailable = $n->remaining + $bonusN2 + $bonusN1;
        
        return [
            'n2' => [
                'year' => $currentYear - 2,
                'remaining' => $n2->remaining,
                'bonus' => $bonusN2,
            ],
            'n1' => [
                'year' => $currentYear - 1,
                'remaining' => $n1->remaining,
                'bonus' => $bonusN1,
            ],
            'n' => [
                'year' => $currentYear,
                'quota' => $n->quota,
                'used' => $n->used,
                'remaining' => $n->remaining,
            ],
            'total_available' => $totalAvailable,
        ];
    }
}