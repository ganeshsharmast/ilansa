<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Cart;
use App\Models\User;
use App\Models\ServiceProducts;
use App\Models\Status;

class OrderProducts extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'order_products';
    
    protected $fillable = [
        'order_id',
        'product_id',
        'status',
    ];
    
    public function cart()
    {
        return $this->belongsTo(Cart::class,'id','cart_id');
    }

    public function providerDetails()
    {
        return $this->belongsTo(User::class,'id','usert_id');
    }
    
    public function product()
    {
        return $this->hasOne(Products::class,'id','product_id');
    }
    
    public function serviceProduct()
    {
        return $this->hasOne(ServiceProducts::class,'id','product_id');
    }
    
    public function productStatus()
    {
        return $this->hasOne(Status::class,'id','status');
    }

}
