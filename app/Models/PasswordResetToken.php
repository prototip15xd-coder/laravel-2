<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['email', 'token'])]
#[Hidden(['token'])]
class PasswordResetToken extends Model //или Authenticatable?
{
    use HasFactory, Notifiable;

    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'email';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false; ///или убрать и сделать автоматическим???

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
