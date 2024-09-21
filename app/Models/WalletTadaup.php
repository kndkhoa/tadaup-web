<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class WalletTadaup extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'wallet_tadaup'; 
    protected $fillable = [
        'walletName',
        'network',
        'value',
        'address',
        'status'
    ];

   


    protected $primaryKey = 'id';

    // You might want to add a method to easily check the user level
    public function hasLevel($levels)
    {
        return in_array($this->level, (array)$levels);
    }

    public static function getAll()
    {
        return self::all();
    }
}
