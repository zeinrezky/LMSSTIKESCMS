<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Repositories\MasterDataRepository;

class UserManagementController extends Controller
{
    protected $dosen;
    protected $masterDataRepository;

    public function __construct(Dosen $dosen, MasterDataRepository $masterDataRepository)
    {
        $this->model = $dosen;
        $this->__route = "user-management";
        $this->service = (new UserService())->setRoute("user-management");
        $this->masterDataRepository = $masterDataRepository;
        view()->share("__route", $this->__route);
        view()->share("__menu", "User Management");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        // dd(auth()->attempt(["email" => "admin@admin.com", "password" => 123]));
        return view("user-management.index");
    }

    public function getCreate()
    {
        return view("user-management.form", [
            "model" => $this->model,
            "titleAction" => "Tambah Data",
            "roles" => $this->masterDataRepository->roles(),
        ]);
    }

    public function postCreate(UserRequest $request)
    {
        $this->service->create($request);
        toast('Data telah disimpan', 'success');
        return redirect($this->__route);
    }

    public function getUpdate($id)
    {
        return view("user-management.form", [
            "model" => $this->model->findOrFail($id),
            "titleAction" => "Edit Data",
            "roles" => $this->masterDataRepository->roles(),
        ]);
    }

    public function postUpdate(UserRequest $request, $id)
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
