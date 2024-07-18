<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Status;
use App\Models\User;

class UserServiceRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     */
    protected $fillable = [
        'message',
    ];

    protected $table="user_service_requests";

    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }
        
    public function userDetails()
        {
            return $this->hasOne(User::class,'id','user_id');
        }
        
}
