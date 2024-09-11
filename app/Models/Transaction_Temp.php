<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Transaction_Temp extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'transactions_temp'; 
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'amount',
        'currency',
        'eWallet',
        'transactionHash',
        'bank_name',
        'bank_account',
        'bank_city',
        'fullname',
        'status',
        'errorMsg',
        'origPerson',
        'created_at',
        'updated_at'
    ];

    public static function recordTransaction($user_id, $request, $type, $status, $currency)
    {
        // $lastTransaction = self::where('user_id', $user)->latest('id')->first();
        // $lastBalance = $lastTransaction ? $lastTransaction->balance_after : 0;

        // $newBalance = ($type === 'DEPOSIT') ? $lastBalance + $amount : $lastBalance - $amount;
        $currency = 'USD';
        return self::create([
            'user_id' => $user_id,
            'type' => $type,
            'amount' => $request->amount,
            'currency' => $currency,
            'bank_name' => $request->bankList,
            'bank_account' => $request->bank_account,
            'bank_city' => $request->bank_city,
            'fullname' => $request->fullname,
            'status' => $status,
        ]);
    }

   
   

    protected $primaryKey = 'id';
    public static function getAll()
    {
        return self::all();
    }
}
