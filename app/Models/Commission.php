<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;
    protected $table = 'commissions';

    const DIRECT_COMMISSION_PERCENTAGE = 5;
    const COMMISSION_RATE = 0.05;
    const INITIAL_AMOUNT = 100;

    protected $fillable = [
        'user_id',
        'amount',
        'commission_percentage',
        'total_referrals'
    ];
    public function commission()
    {
        return $this->belongsTo(User::class, 'user_id','referred_by');
    }
}
