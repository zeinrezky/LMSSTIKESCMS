<?php

namespace App\Repositories;

use App\Models\MasterData;

class MasterDataRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new MasterData();
    }

    public function roles()
    {
        return $this->model->select("sub_kategori", "idmaster_data")
            ->where("kategori", "PM_Role")
            ->pluck("sub_kategori", "idmaster_data")
            ->toArray();
    }
}
