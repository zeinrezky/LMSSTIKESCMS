<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePenilaian extends Model
{
    protected $primaryKey = "id";

    protected $table = "metode_penilaian";

    public $timestamps = false;

    public $guarded = [];
}
