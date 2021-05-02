<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\PmAssign;
use Illuminate\Support\Str;
use App\Singleton\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): UserService
    {
        $this->route = $route;

        return $this;
    }

    public function getData(Request $request)
    {
        $model = Dosen::select('id_dosen', 'nip', 'nama', 'email', 'telepon')
            ->where("is_dosen", 0);
            // ->orderBy("id_dosen", "desc");

        return \Table::of($model)
            ->addColumn('action', function ($model) {

                $edit = Component::build()
                    ->url($this->route . "/update/" . $model->id_dosen)
                    ->type("edit")
                    ->link();

                $delete = Component::build()
                    ->url($this->route . "/delete/" . $model->id_dosen)
                    ->type("delete")
                    ->link();

                return $edit . " " . $delete;
            })
            ->make();
    }

    public function create(Request $request)
    {
        $foto = $request->file("foto");
        $fotoName = "";
        if (!empty($foto)) {

            $fotoName = Str::random(5) . "." . $foto->getClientOriginalExtension();

            $foto->storeAs("public/contents", $fotoName);
        }

        $inputs = $request->all();
        $inputs["foto"] = $fotoName;
        $inputs["is_dosen"] = 0;

        Dosen::create($inputs);
    }

    public function update(Request $request, int $id)
    {
        $model = Dosen::findOrFail($id);
        $foto = $request->file("foto");
        $fotoName  = $model->foto;

        if ($request->delete_foto != $model->foto) {
            $fotoName = "";
            \Storage::delete(contents_path($model->foto));
        }

        if (!empty($foto)) {

            $fotoName = Str::random(5) . "." . $foto->getClientOriginalExtension();

            $foto->storeAs("public/contents", $fotoName);
        }

        $inputs = $request->all();
        $inputs["foto"] = $fotoName;
        $inputs["is_dosen"] = 0;
        $inputs["password_plain"] = $request->password;
        $inputs["password"] = Hash::make($request->password);

        $model->update($inputs);
    }

    public function delete($model)
    {

        $check = PmAssign::where('sme_id',$model['id_dosen'])
                           ->orWhere('reviewer_id',$model['id_dosen'])
                           ->orWhere('approval_id',$model['id_dosen'])
                           ->exists();
                           
        if (!$check) {
            try {
                \Storage::delete(contents_path($model->foto));
                $model->delete();
                return true;
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

        return false;
    }
}
