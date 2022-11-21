<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetodePenilaianRequest;
use App\Models\MetodePenilaian;
use App\Services\MetodePenilaianService;
use Illuminate\Http\Request;

class MetodePenilaianController extends Controller
{
    public function __construct(MetodePenilaian $metodePenilaian)
    {
        $this->model = $metodePenilaian;
        $this->__route = "metode-penilaian";
        $this->service = (new MetodePenilaianService())->setRoute("metode-penilaian");
        view()->share("__route", $this->__route);
        view()->share("__menu", "Metode Penilaian");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("metode-penilaian.index");
    }

    public function getCreate()
    {
        return view("metode-penilaian.form", [
            "model" => $this->model,
            "titleAction" => "Tambah Data",
        ]);
    }

    public function postCreate(MetodePenilaianRequest $request)
    {
        $this->service->create($request);
        toast('Data telah disimpan', 'success');
        return redirect($this->__route);
    }

    public function getUpdate($id)
    {
        $model = $this->model->findOrfail($id);

        return view("metode-penilaian.form", [
            "model" => $model,
            "titleAction" => "Edit Data",
        ]);
    }

    public function postUpdate(MetodePenilaianRequest $request, $id)
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
