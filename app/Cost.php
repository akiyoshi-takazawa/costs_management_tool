<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cost extends Model
{
    use softDeletes;
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
       'user_id', 'proposition_id', 'week_id', 'cost', 'status', 'comment','submit_at', 'auth_comment',
    ];
    
    public function proposition()
    {
        return $this->belongsTo(Proposition::class);
    }
    
    public function week()
    {
        return $this->belongsTo(Week::class, 'week_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
