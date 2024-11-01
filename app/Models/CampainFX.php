<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CampainFX extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'campainFX'; 
    protected $fillable = [
        'campainID',
        'campainName',
        'campainDescription',
        'content',
        'campain_amount',
        'fromDate',
        'toDate',
        'ewalletAddress',
        'network',
        'currency',
        'status',
        'profitMLM',
        'profitPercent',
        'origPerson',
        'created_at',
        'updated_at'
    ];

    public static function recordTransaction($request)
    {
        return self::create([
            'campainName' => $request->campain_name,
            'campainDescription' => $request->campain_description,
            'content' => $request->campain_content,
            'campain_amount' => $request->campain_amount,
            'fromDate' => $request->from_date,
            'toDate' => $request->to_date,
            'ewalletAddress' => $request->ewallet_address,
            'network' => $request->ewallet_network,
            'currency' => 'USDT',
            'status' => 'ORIG',
        ]);
    }

    protected $primaryKey = 'campainID';
    public static function getAll()
    {
        return self::all();
    }
}
