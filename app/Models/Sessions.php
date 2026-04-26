<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'user_id',
    'ip_address',
    'user_agent',
    'payload',
    'last_activity'
])]

class Sessions extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; /// или все таки лучше сделать id int?
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'last_activity' => 'integer',
        ];
    }

    public function user(): User
    {
        return $this->belongsTo(User::class);
    }

//    public function sessions()
//    {
//        return $this->hasMany(Session::class, 'user_id');
//    }
}
