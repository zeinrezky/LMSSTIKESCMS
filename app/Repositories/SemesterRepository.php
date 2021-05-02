<?php

namespace App\Repositories;

use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class SemesterRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Semester();
    }

    public function listsBox(): array
    {
        return $this->model->select("nama_semester", "id_semester")
            ->pluck("nama_semester", "id_semester")
            ->toArray();
    }
}
