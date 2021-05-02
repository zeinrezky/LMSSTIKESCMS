<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Services\DosenService;
use App\Http\Requests\DosenRequest;

class DosenController extends Controller
{
    protected $dosen;

    public function __construct(Dosen $dosen)
    {
        $this->model = $dosen;
        $this->__route = "dosen";
        $this->service = (new DosenService())->setRoute("dosen");
        view()->share("__route", $this->__route);
        view()->share("__menu", "Dosen");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("dosen.index");
    }

    public function getCreate()
    {
        return view("dosen.form", [
            "model" => $this->model,
            "titleAction" => "Tambah Data",
        ]);
    }

    public function postCreate(DosenRequest $request)
    {
        $this->service->create($request);
        toast('Data telah disimpan', 'success');
        return redirect($this->__route);
    }

    public function getUpdate($id)
    {
        return view("dosen.form", [
            "model" => $this->model->findOrFail($id),
            "titleAction" => "Edit Data",
        ]);
    }

    public function postUpdate(DosenRequest $request, $id)
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
