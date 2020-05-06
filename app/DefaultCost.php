<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DefaultCost extends Model
{
    use softDeletes;
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
       'cost',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function proposition()
    {
        return $this->belongsTo(Proposition::class, 'proposition_id');
    }
}
