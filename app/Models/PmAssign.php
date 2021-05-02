<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmAssign extends Model
{
    protected $primaryKey = "id_pm_assign";

    protected $table = "pm_assign";

    public $timestamps = false;

    protected $guarded = [];
}
