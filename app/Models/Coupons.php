<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CouponType;
use App\Models\Status;

class Coupons extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'coupons'; 
    protected $fillable = [
        'coupon_type',
        'name',
        'code',
        'value',
        'status',
    ];
    
        public function CouponType()
    {
        return $this->hasOne(CouponType::class,'id','coupon_type');
    }
    
    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }


}
