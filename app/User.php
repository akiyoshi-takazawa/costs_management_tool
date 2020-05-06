<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use softDeletes;
    
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'name', 'user_type' 
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
     
     public function propositions()
    {
        return $this->hasMany(Proposition::class, 'authorizer_user_id');
    }
    
     public function default_costs()
    {
        return $this->hasMany(DefaultCost::class, 'user_id');
    }
    
     public function costs()
    {
        return $this->hasMany(Cost::class, 'user_id');
    }
    
}
