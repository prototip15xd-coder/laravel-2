<?php

declare(strict_types=1);

namespace App\Models;

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
