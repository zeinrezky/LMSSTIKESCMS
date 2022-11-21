<?php

namespace App\Repositories;

use App\Models\Dosen;
use Illuminate\Support\Facades\DB;

class DosenRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Dosen();
    }

    public function listsBox(): array
    {
        return $this->model->select(
            DB::raw("CONCAT(NIP,' - ',nama) as nip_nama"),
            "id_dosen"
        )
            ->where("is_dosen", 1)
            ->pluck("nip_nama", "id_dosen")
            ->toArray();
    }
}
