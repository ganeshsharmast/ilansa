<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use App\Models\ProductCategory;

class CartProducts extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'status',
    ];
    
        public function getCart()
    {
        return $this->belongsTo(Cart::class,'id','cart_id');
    }

    public function productCategory()
    {
        return $this->hasOne(ServiceProducts::with('productCategory'),'product_id','product_id');
    }

}
