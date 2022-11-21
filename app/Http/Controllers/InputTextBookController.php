<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputTextBookRequest;
use App\Models\PengembangMateri;
use App\Models\PmAssign;
use App\Models\Semester;
use App\Models\TextBook;
use Illuminate\Http\Request;
use App\Services\InputTextBookService;

class InputTextBookController extends Controller
{
    public function __construct(PmAssign $pmAssign, PengembangMateri $pengembangMateri, TextBook $textBook)
    {
        $this->pmAssign = $pmAssign;
        $this->__route = "input-text-book";
        $this->service = (new InputTextBookService())->setRoute("input-text-book");
        $this->pengembangMateri = $pengembangMateri;
        $this->textBook = $textBook;
        view()->share("__route", $this->__route);
        view()->share("__menu", "Entry Text Book");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex(Request $request)
    {
        return view("input-text-book.index")->with('request',$request->all());
    }

    public function getDetail($id)
    {
        $pengembangMateri = $this->pengembangMateri->findOrFail($id);
        $model = $pengembangMateri->text_book()->count() > 0 ? $pengembangMateri->text_book : $this->textBook;
        return view("input-text-book.form", [
            "pengembangMateri" => $pengembangMateri,
            "model" => $model,
            "titleAction" => "Text Book",
        ]);
    }

    public function postDetail(InputTextBookRequest $request, $id)
    {
        $this->service->updateOrCreate($request, $id);
        toast('Data telah diupdate', 'success');
        return redirect($this->__route);
    }
}
