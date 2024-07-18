<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Cart;
use App\Models\User;
use App\Models\Company;
use App\Models\OrderStatus;
use App\Models\WorkStatus;

class OrderRequest extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'order_request';
    
    protected $fillable = [
        'order_id',
        'provider_id',
        'status',
    ];
    
    public function cart()
    {
        return $this->belongsTo(Cart::class,'id','cart_id');
    }

    public function providerDetails()
    {
        return $this->belongsTo(User::class,'provider_id','id');
    }
    
    public function companyDetails()
    {
        return $this->belongsTo(Company::class,'provider_id','user_id');
    }

    public function orderStatus()
        {
            return $this->hasOne(OrderStatus::class,'id','status');
        }
        
    public function workStatusDetails()
        {
            return $this->hasOne(WorkStatus::class,'id','work_status');
        }
}
