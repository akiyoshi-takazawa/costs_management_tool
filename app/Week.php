<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Week extends Model
{
    use softDeletes;
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
       'year', 'month', 'week', 'start_date', 'end_date',
    ];
    
     public function cost()
    {
        return $this->hasMany(Cost::class, 'week_id');
    }
    
    
}
