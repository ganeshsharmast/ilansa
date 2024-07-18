<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SubServices;

class ProviderSubServices extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'sub_service_id',
        'status',
    ];
    

    public function subServices()
    {
        return $this->hasOne(SubServices::class,'id','sub_service_id');
    }
    
        public function providerInfo()
    {
        return $this->hasOne(User::class,'id','provider_id');
    }

}
