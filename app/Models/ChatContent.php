<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;
use App\Models\User;

class ChatContent extends Model
{
    protected $table="chat_contents";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'status',
    ];
    
     /**
     * Get the phone associated with the user.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class,'id','chat_id');
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
