<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Services;
use App\Models\ProductCategory;
use App\Models\SubServices;

class ServiceProducts extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

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
    
        public function getCart()
    {
        return $this->belongsTo(Cart::class,'id','cart_id');
    }

    public function productCategory()
    {
        return $this->hasOne(Service::class,'product_id','product_id');
    }
    
    public function product()
    {
        return $this->hasOne(Products::class,'id','product_id');
    }
    
    public function subService()
    {
        return $this->hasOne(SubServices::class,'id','sub_service_id');
    }

}
