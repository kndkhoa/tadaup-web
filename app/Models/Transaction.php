<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ID',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'currency',
        'created_at',
        'updated_at'
    ];

    public static function recordTransaction($user, $type, $amount, $request)
    {
        $lastTransaction = self::where('user_id', $user)->latest('id')->first();
        $lastBalance = $lastTransaction ? $lastTransaction->balance_after : 0;

        $newBalance = ($type === 'DEPOSIT') ? $lastBalance + $amount : $lastBalance - $amount;
        $currency = $request->campaign_id ? $request->campaign_id : 'USD';
        return self::create([
            'user_id' => $user,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $newBalance,
            'currency' => $currency
        ]);
    }

   
   

    protected $primaryKey = 'ID';
    public static function getAll()
    {
        return self::all();
    }
}
