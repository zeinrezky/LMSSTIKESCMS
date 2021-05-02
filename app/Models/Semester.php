<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $primaryKey = "id_semester";

    protected $table = "semester";

    public $timestamps = false;

    public $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->kode_semester = \Str::random(5);
        });
    }
}
