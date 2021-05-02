<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengembangMateri extends Model
{
    protected $primaryKey = "id_pm";

    protected $table = "pengembang_materi";

    public $timestamps = false;

    protected $guarded = [];

    public function pm_assign()
    {
        return $this->hasOne(PmAssign::class, 'id_pm');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, "id_semester");
    }

    public function matakuliah()
    {
        return $this->belongsTo(MataKuliah::class, "id_matakuliah");
    }

    public function text_book()
    {
        return $this->hasOne(TextBook::class, "id_pm");
    }

    public function rps()
    {
        return $this->hasOne(Rps::class, "id");
    }

    public function or()
    {
        return $this->hasOne(OrModel::class, "id");
    }
}
