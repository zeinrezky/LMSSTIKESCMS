<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignDosenRequest;
use Illuminate\Http\Request;
use App\Models\PengembangMateri;
use App\Services\AssignDosenService;
use App\Repositories\DosenRepository;
use App\Repositories\SemesterRepository;
use App\Repositories\MataKuliahRepository;

class AssignDosenController extends Controller
{
    public function __construct(PengembangMateri $pengembangMateri)
    {
        $this->pengembangMateri = $pengembangMateri;
        $this->__route = "assign-dosen";
        $this->service = (new AssignDosenService())->setRoute("assign-dosen");

        $this->dosenRepository = new DosenRepository();
        $this->dosenListsBox = ["" => "-- Pilih Dosen --"] + $this->dosenRepository->listsBox();

        $this->semesterRepository = new SemesterRepository();
        $this->semesterListsBox = ["" => "-- Pilih Semester --"] + $this->semesterRepository->listsBox();

        $this->matakuliahRepository = new MataKuliahRepository();
        $this->matakuliahListsBox = ["" => "-- Pilih Mata Kuliah --"] + $this->matakuliahRepository->listsBox();

        view()->share("__route", $this->__route);
        view()->share("__menu", "Assign Dosen");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        // dd(session()->get('user.dosen')[0]->is_dosen);
        return view("assign-dosen.index");
    }

    public function getCreate()
    {
        return view("assign-dosen.form", [
            "model" => $this->pengembangMateri,
            "titleAction" => "Tambah Data",
            "dosenListsBox" => $this->dosenListsBox,
            "semesterListsBox" => $this->semesterListsBox,
            "matakuliahListsBox" => $this->matakuliahListsBox,
        ]);
    }

    public function postCreate(AssignDosenRequest $request)
    {
        $this->service->create($request);
        toast('Data telah disimpan', 'success');
        return redirect($this->__route);
    }

    public function getUpdate($id)
    {
        return view("assign-dosen.form", [
            "model" => $this->pengembangMateri->findOrFail($id),
            "titleAction" => "Edit Data",
            "dosenListsBox" => $this->dosenListsBox,
            "semesterListsBox" => $this->semesterListsBox,
            "matakuliahListsBox" => $this->matakuliahListsBox,
        ]);
    }

    public function postUpdate(AssignDosenRequest $request, $id)
    {
        $this->service->update($request, $id);
        toast('Data telah diupdate', 'success');
        return redirect($this->__route);
    }

    public function getDelete($id)
    {
        $model = $this->pengembangMateri->findOrFail($id);
        $del = $this->service->delete($model);
        
        if ($del) {
            toast('Data telah dihapus', 'success');
        }else{
            toast('Data tidak bisa dihapus, karena data sudah terpakai', 'warning');
        }
        return redirect($this->__route);
    }
}
