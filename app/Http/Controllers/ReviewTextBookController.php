<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTextBookRequest;
use App\Models\PengembangMateri;
use App\Models\PmAssign;
use App\Models\Semester;
use App\Models\TextBook;
use Illuminate\Http\Request;
use App\services\ReviewTextBookService;

class ReviewTextBookController extends Controller
{
    public function __construct(PmAssign $pmAssign, PengembangMateri $pengembangMateri, TextBook $textBook)
    {
        $this->pmAssign = $pmAssign;
        $this->__route = "review-text-book";
        $this->service = (new ReviewTextBookService())->setRoute("review-text-book");
        $this->pengembangMateri = $pengembangMateri;
        $this->textBook = $textBook;
        view()->share("__route", $this->__route);
        view()->share("__menu", "Review Text Book");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("review-text-book.index");
    }

    public function getDetail($id)
    {
        $pengembangMateri = $this->pengembangMateri->findOrFail($id);
        $model = $pengembangMateri->text_book()->count() > 0 ? $pengembangMateri->text_book : $this->textBook;

        $userStatus = $this->service->userStatus($id);
        
        return view("review-text-book.form", [
            "pengembangMateri" => $pengembangMateri,
            "model" => $model,
            "userStatus" => $userStatus,
            "titleAction" => "Text Book",
        ]);
    }

    public function postDetail(ReviewTextBookRequest $request, $id)
    {
        $this->service->ReviewOrApproval($request, $id);
        toast('Data telah diupdate', 'success');
        return redirect($this->__route);
    }
}
