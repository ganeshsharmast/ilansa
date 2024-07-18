<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartProducts;
use App\Models\Services;
use App\Models\SubServices;
use App\Models\ProductCategory;
use App\Models\OrderStatus;

class ProductCategory extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    protected $table="product_category";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
    ];
    
    public function productCategory()
        {
            return $this->hasOne(ProductCategory::class,'id','product_category_id');
        }
    
    public function productStatus()
        {
            return $this->hasOne(OrderStatus::class,'id','status');
        }   
        
        public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }    

}
