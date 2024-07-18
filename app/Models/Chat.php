<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChatContent;

class Chat extends Model
{
    //use HasApiTokens, HasFactory, Notifiable;
     protected $table = "chats";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];
    
     /**
     * Get the phone associated with the user.
     */
    public function chatContents()
    {
        return $this->hasMany(ChatContent::class,'chat_id','id');
    }
    
    public function statusDetails()
    {
        return $this->hasOne(Status::class,'id','status');
    }
    
    
    public function userDetails()
    {
        return $this->hasOne(User::class,'id','sender_id');
    }
    
    
    public function receiverDetails()
    {
        return $this->hasOne(User::class,'id','receiver_id');
    }
}
