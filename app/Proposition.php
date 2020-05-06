<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposition extends Model
{
    use softDeletes;
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'name', 'client_name', 'input_id', 'authorizer_user_id', 'start_date', 'end_date'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'authorizer_user_id');
    }
    
    public function default_costs()
    {
        return $this->hasMany(DefaultCost::class, 'proposition_id');
    }
    
    public function costs()
    {
        return $this->hasmany(Cost::class, 'proposition_id');
    }
}
