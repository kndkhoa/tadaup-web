<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerAdditional extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'customer_additional';
    protected $fillable = [
        'customer_id',
        'gameScore',
        'gameChainCorrect',
        'gameTurn',
        'creditMainAddress',
        'creditSecondaryAddress',
        'creditDegree',
        'creditGender',
        'creditMarriage',
        'creditJob',
        'creditCompany',
        'creditCompanyDetail',
        'creditContactName1',
        'creditContactPhone1',
        'creditContactRelationship1',
        'creditContactName2',
        'creditContactPhone2',
        'creditContactRelationship2',
        'activeLastest',
        'activeOffline',
        'activeDiary',
        'freetokenDone',
        'proTrader',
    ];


  
    protected $primaryKey = 'customer_id';

    public static function getAll()
    {
        return self::all();
    }
}
