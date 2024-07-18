<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Status;
use App\Models\AccountType;


class Company extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
    
    protected $table = "company";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'name'
    ];
    
     public function accountType()
    {
        return $this->hasOne(AccountType::class,'id','account_type_id');
    }

    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }

}
