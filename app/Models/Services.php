<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// namespace App\Models\Status;

class Services extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $table ="services";
    protected $fillable = [
        'name',
        'status',
    ];
    
    public function statusDetails()
        {
            return $this->hasOne(Status::class,'id','status');
        }
}
