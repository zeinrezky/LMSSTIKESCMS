<?php

namespace App\Services;

use App\Models\MataKuliah;
use App\Models\PengembangMateri;
use App\Singleton\Component;
use Illuminate\Http\Request;

class MataKuliahService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): MataKuliahService
    {
        $this->route = $route;

        return $this;
    }

    public function getData(Request $request)
    {
        $model = MataKuliah::select('id_matakuliah', 'mk_kode', 'mk_nama','sks_praktikum','sks_tatap_muka');
            // ->orderBy("id_matakuliah", "desc");

        return \Table::of($model)
            ->addColumn('action', function ($model) {

                $edit = Component::build()
                    ->url($this->route . "/update/" . $model->id_matakuliah)
                    ->type("edit")
                    ->link();

                $delete = Component::build()
                    ->url($this->route . "/delete/" . $model->id_matakuliah)
                    ->type("delete")
                    ->link();

                return $edit . " " . $delete;
            })
            ->make();
    }

    public function create(Request $request)
    {
        MataKuliah::create($request->all());
    }

    public function update(Request $request, int $id)
    {
        $model = MataKuliah::findOrFail($id);

        $model->update($request->all());
    }

    public function delete($model)
    {
        $check = PengembangMateri::where('id_matakuliah',$model['id_matakuliah'])->exists();

        if (!$check) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return true;
        }
    }
}
