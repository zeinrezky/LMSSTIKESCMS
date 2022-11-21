<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'nip', 'name', 'password', 'plain_password', 'email', 'phone', 'address', 'gender', 'role', 'status', 'image'
    ];

    protected $hidden = [
        'password'
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
