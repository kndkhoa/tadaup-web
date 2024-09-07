<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerConnection extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'cutomer_connection';
    protected $fillable = [
        'customer_id',
        'link_url',
        'user_name',
        'password',
        'type',
        'status'
    ];


  
    protected $primaryKey = 'id';

    public static function getAll()
    {
        return self::all();
    }

    public static function addRecord($request)
    {
        return self::create([
            'customer_id' => $request->customerid,
            'link_url' => $request->link,
            'user_name' => $request->username,
            'password' => $request->password,
            'type' => $request->type,
            'status' => 'ACTIVE'
        ]);
    }
}
