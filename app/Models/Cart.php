<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartProducts;
use App\Models\Coupons;

class Cart extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
    ];
    
     /**
     * Get the phone associated with the user.
     */
    public function cartProducts()
    {
        return $this->hasMany(CartProducts::class,'cart_id','id');
    }
    
        public function coupons()
    {
        return $this->hasOne(Coupons::class,'id','coupon_id');
    }
}
