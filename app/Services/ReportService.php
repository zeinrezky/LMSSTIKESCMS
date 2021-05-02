<?php

namespace App\Services;

use App\Models\PmAssign;
use App\Models\Rps;
use Illuminate\Support\Str;
use App\Singleton\Component;
use Illuminate\Http\Request;
use App\Models\PengembangMateri;
use Exception;
use Illuminate\Support\Facades\DB;

class ReportService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): ReportService
    {
        $this->route = $route;

        return $this;
    }

    public function getDataKP(Request $request)
    {
        $model = PengembangMateri::selectRaw('pengembang_materi.id_pm,
                                              nama_semester,
                                              pengembang_materi.id_semester,
                                              pengembang_materi.id_matakuliah,
                                              mk_nama,mk_kode')
            ->leftJoin('pm_assign','pengembang_materi.id_pm','=','pm_assign.id_pm')
            ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->groupBy(['pengembang_materi.id_semester','pengembang_materi.id_matakuliah']);
            // ->orderBy("pengembang_materi.id_pm", "desc");

        return \Table::of($model)
            ->addColumn('action', function ($model) {

                $edit = Component::build()
                    ->url($this->route . "/kemajuan-perkembangan/" . $model->id_semester."/".$model->id_matakuliah)
                    ->type("view")
                    ->link();

                return $edit;
            })
            ->make();
    }

    public function getDataSS(Request $request)
    {
        $model = PengembangMateri::selectRaw('pengembang_materi.id_pm,
                                              nama_semester,
                                              pengembang_materi.id_semester,
                                              pengembang_materi.id_matakuliah,
                                              mk_nama,mk_kode')
            ->leftJoin('pm_assign','pengembang_materi.id_pm','=','pm_assign.id_pm')
            ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->groupBy(['pengembang_materi.id_semester','pengembang_materi.id_matakuliah']);
            // ->orderBy("pengembang_materi.id_pm", "desc");

        return \Table::of($model)
            ->addColumn('action', function ($model) {

                $edit = Component::build()
                    ->url($this->route . "/status-silabus/" . $model->id_semester."/".$model->id_matakuliah)
                    ->type("view")
                    ->link();

                return $edit;
            })
            ->make();
    }
}
