<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;

class WorkStatus extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'work_status';
    
    protected $fillable = [
        'name',
        'image',
        'type',
    ];
    
    // public function status()
    // {
    //     return $this->hasOne(Status::class,'id','status');
    // }

}
