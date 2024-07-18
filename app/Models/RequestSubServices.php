<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Services;

class RequestSubServices extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'subject',
        'description',
        'status',
    ];
    
      public function services()
        {
            return $this->hasOne(Services::class,'id','service_id');
        }
}
