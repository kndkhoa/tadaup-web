<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerReport extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'customer_report';
    protected $fillable = [
        'customer_id',
        'reportNet',
        'reportVolume',
        'reportExchange',
        'reportDate',
    ];


  
    protected $primaryKey = 'customer_id';

    public static function getAll()
    {
        return self::all();
    }
}
