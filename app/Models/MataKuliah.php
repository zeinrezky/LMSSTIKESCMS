<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $primaryKey = "id_matakuliah";

    protected $table = "matakuliah";

    public $timestamps = false;

    public $guarded = [];
}
