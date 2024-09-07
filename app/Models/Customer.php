<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'customers'; 
    protected $fillable = [
        'customer_id',
        'user_id',
        'role_id',
        'full_name',
        'phone',
        'address',
        'image_font_id',
        'image_back_id',
        'bank_account',
        'bank_name',
        'ewalletAddress',
        'interestEwallet',
        'ewalletNetwork',
        'user_sponser_id'
    ];

    public function descendants()
    {
        return $this->hasMany(Customer::class, 'user_sponser_id')->with('descendants');
    }

    public function ancestors()
    {
        return $this->belongsTo(Customer::class, 'user_sponser_id')->with('ancestors');
    }


    public function parent()
    {
        return $this->belongsTo(Customer::class, 'user_sponser_id', 'user_id');
    }

    public function children()
    {
        return $this->hasMany(Customer::class, 'user_sponser_id', 'user_id');
    }

    // // Recursive relationship to fetch all descendants
    // public function descendants()
    // {
    //     return $this->children()->with('descendants');
    // }
   
    public static function getCustomerTreeForId($customerId)
    {
        $customerTree = Customer::findOrFail($customerId);
 
        if (!$customerTree) {
            return collect();
        }
 
        $allCustomers = collect([$customerTree]);
 
        //self::loadParents($customerTree, $allCustomers);
        self::loadChildren($customerTree, $allCustomers);
 
        return $allCustomers;
    }
 
    private static function loadParents($customer, &$allCustomers)
    {
        $parent = $customer->parent;
        if ($parent) {
            $allCustomers->push($parent);
            self::loadParents($parent, $allCustomers);
        }
    }
 
    private static function loadChildren($customer, &$allCustomers)
    {
        $children = $customer->children;
        if ($children->isNotEmpty()) {
            $allCustomers = $allCustomers->merge($children);
            foreach ($children as $child) {
                self::loadChildren($child, $allCustomers);
            }
        }
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'hashed',
    // ];

    protected $primaryKey = 'user_id';
    
    public static function getAll()
    {
        return self::all();
    }
}
