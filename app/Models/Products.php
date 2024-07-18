<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartProducts;
use App\Models\Services;
use App\Models\SubServices;
use App\Models\ProductCategory;
use App\Models\OrderStatus;

class Products extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    protected $table="products";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'product_id',
        'status',
    ];
    
    public function cartProducts()
        {
            return $this->belongsTo(CartProducts::class,'id','product_id');
        }

    public function services()
        {
            return $this->hasOne(SubServices::class,'id','product_id');
        }
    
    public function productCategory()
        {
            return $this->hasOne(ProductCategory::class,'id','product_category_id');
        }
    
    public function productStatus()
        {
            return $this->hasOne(OrderStatus::class,'id','status');
        }   

}
