<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CampainFX_Txn extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'campainFX_Txn';
    protected $fillable = [
        'id',
        'campainID',
        'customerID',
        'ewalletCustomerID',
        'ewalletAdressCampain',
        'txnType',
        'amount',
        'transactionHash',
        'percent',
        'status',
        'created_at',
        'updated_at'
    ];

    public static function recordTransaction($request, $customer_id, $percent, $type)
    {
        return self::create([
            'campainID' => $request->campain_id,
            'customerID' => $customer_id,
            'ewalletCustomerID' => $request->ewallet_cutomer_id,
            'ewalletAdressCampain' => $request->ewallet_campain_id,
            'txnType' => $type,
            'amount' => $request->amount_deposit,
            'transactionHash' => $request->transactionHash,
            'percent' => $percent,
            'status' => 'N'
        ]);
    }

    public static function submitPayment($campainFXTXN, $amount, $type)
    {
        return self::create([
            'campainID' => $campainFXTXN->campainID,
            'customerID' => $campainFXTXN->customerID,
            'ewalletCustomerID' => $campainFXTXN->ewalletCustomerID,
            'ewalletAdressCampain' => $campainFXTXN->ewalletAdressCampain,
            'txnType' => $type,
            'amount' => $amount,
            'transactionHash' => $campainFXTXN->transactionHash,
            'percent' => $campainFXTXN->percent,
            'status' => 'N'
        ]);
    }

   
   

    protected $primaryKey = 'id';
    public static function getAll()
    {
        return self::all();
    }
}
