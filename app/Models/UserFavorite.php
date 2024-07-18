<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserFavorite extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'user_favorite';
    
    protected $fillable = [
        'user_id',
        'provider_id'
    ];
    

    public function providerFavorite()
        {
            return $this->hasOne(User::class,'id','provider_id');
        }
        
    public function userFavorite()
        {
            return $this->hasOne(User::class,'id','user_id');
        }
    
}
