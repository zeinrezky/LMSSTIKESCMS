<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use App\Services\SemesterService;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function __construct(Semester $semester)
    {
        $this->model = $semester;
        $this->__route = "semester";
        $this->service = (new SemesterService())->setRoute("semester");
        view()->share("__route", $this->__route);
        view()->share("__menu", "Semester");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("semester.index");
    }

    public function getCreate()
    {
        return view("semester.form", [
            "model" => $this->model,
            "titleAction" => "Tambah Data",
            "fromMonth" => (int) date("m"),
            "fromYear" => date("Y"),
            "toMonth" => (int) date("m"),
            "toYear" => date("Y"),
        ]);
    }

    public function postCreate(SemesterRequest $request)
    {
        $this->service->create($request);
        toast('Data telah disimpan', 'success');
        return redirect($this->__route);
    }

    public function getUpdate($id)
    {
        $model = $this->model->findOrfail($id);

        $from = carbon()->parse($model->from);
        $fromMonth = (int) $from->format("m");
        $fromYear = (int) $from->format("Y");

        $to = carbon()->parse($model->to);
        $toMonth = (int) $to->format("m");
        $toYear = (int) $to->format("Y");

        return view("semester.form", [
            "model" => $model,
            "titleAction" => "Tambah Data",
            "fromMonth" => $fromMonth,
            "fromYear" => $fromYear,
            "toMonth" => $toMonth,
            "toYear" => $toYear,
        ]);
    }

    public function postUpdate(SemesterRequest $request, $id)
    {
        $this->service->update($request, $id);
        toast('Data telah diupdate', 'success');
        return redirect($this->__route);
    }

    public function getDelete($id)
    {
        $model = $this->model->findOrFail($id);
        $del = $this->service->delete($model);
        
        if ($del) {
            toast('Data telah dihapus', 'success');
        }else{
            toast('Data tidak bisa dihapus, karena data sudah terpakai', 'warning');
        }
        return redirect($this->__route);
    }
}
