<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dosen extends Authenticatable
{
    use Notifiable;

    protected $guarded = ['verifikasi_password'];

    protected $primaryKey = "id_dosen";

    public $timestamps = false;

    protected $table = "dosen";

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->flag_login = 1;
            $model->password_plain = $model->password;
            $model->password = \Hash::make($model->password);
        });

        // self::updating(function ($model) {
        //     $model->password_plain = $model->password;
        //     $model->password = \Hash::make($model->password);
        // });
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    protected $rememberTokenName = false;

    public function setAttribute($key, $value)
        {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
            {
              parent::setAttribute($key, $value);
            }
    }
}
