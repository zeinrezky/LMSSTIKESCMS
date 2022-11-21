<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\PengembangMateri;
use Illuminate\Support\Str;
use App\Singleton\Component;
use Illuminate\Http\Request;

class SemesterService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): SemesterService
    {
        $this->route = $route;

        return $this;
    }

    public function getData(Request $request)
    {
        $model = Semester::select('id_semester', 'nama_semester', 'from', 'to');
            // ->orderBy("id_semester", "desc");

        return \Table::of($model)
            ->addColumn('action', function ($model) {

                $edit = Component::build()
                    ->url($this->route . "/update/" . $model->id_semester)
                    ->type("edit")
                    ->link();

                $delete = Component::build()
                    ->url($this->route . "/delete/" . $model->id_semester)
                    ->type("delete")
                    ->link();

                return $edit . " " . $delete;
            })
            ->make();
    }

    public function create(Request $request)
    {
        $from = carbon()
            ->parse($request->dari_tahun . "-" . $request->dari_bulan . "-01")
            ->format("Y-m-d");

        $toString = $request->sampai_tahun . "-" . $request->sampai_bulan . "-01";
        $to = carbon($toString)
            ->parse($toString)
            ->endOfMonth()
            ->format("Y-m-d");

        $inputs = [
            "nama_semester" => $request->nama_semester,
            "create_date" => date("Y-m-d H:i:s"),
            "create_user" => auth()->user()->id,
            "from" => $from,
            "to" => $to,
        ];
        Semester::create($inputs);
    }

    public function update(Request $request, int $id)
    {
        $model = Semester::findOrFail($id);
        $from = carbon()
            ->parse($request->dari_tahun . "-" . $request->dari_bulan . "-01")
            ->format("Y-m-d");

        $toString = $request->sampai_tahun . "-" . $request->sampai_bulan . "-01";
        $to = carbon($toString)
            ->parse($toString)
            ->endOfMonth()
            ->format("Y-m-d");

        $inputs = [
            "nama_semester" => $request->nama_semester,
            "lastup_date" => date("Y-m-d H:i:s"),
            "lastup_user" => auth()->user()->id,
            "from" => $from,
            "to" => $to,
        ];
        $model->update($inputs);
    }

    public function delete($model)
    {
        $check = PengembangMateri::where('id_semester',$model['id_semester'])->exists();

        if (!$check) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return true;
        }

        return false;
    }
}
