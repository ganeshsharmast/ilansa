<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Status;
use App\Models\User;

class Notification extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'subject',
        'status',
    ];
    
    public function orderProducts()
    {
        return $this->hasMany(OrderProducts::class,'order_id','id');
    }

    public function providerDetails()
    {
        return $this->belongsTo(User::class,'id','usert_id');
    }
    
     public function userDetails()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
    

    public function orderStatus()
        {
            return $this->hasOne(OrderStatus::class,'id','status');
        }  
    
    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }     

}
