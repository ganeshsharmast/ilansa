<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Cart;
use App\Models\Coupons;
use App\Models\Status;
use App\Models\OrderStatus;
use App\Models\OrderRequest;
use App\Models\User;

class Orders extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'orders';
    
    protected $fillable = [
        'cart_id',
        'provider_id',
        'status',
    ];
    
    public function orderProducts()
    {
        return $this->hasMany(OrderProducts::class,'order_id','id');
    }

    public function orderRequest()
    {
        return $this->hasMany(OrderRequest::class,'order_id','id');
    }

    public function providerDetails()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function coupons()
    {
        return $this->hasOne(Coupons::class,'id','coupon_id');
    }
    
     public function userDetails()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    
    public function status()
    {
        return $this->hasOne(Status::class,'id','status');
    }

    public function orderStatus()
        {
            return $this->hasOne(OrderStatus::class,'id','status');
        }

    public function userAccept()
        {
            return $this->hasOne(OrderStatus::class,'id','user_acceptance');
        } 
        
    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        } 

}
