<?php

namespace App\services;

use App\Http\Requests\ReviewTextBookRequest;
use App\Singleton\Component;
use Illuminate\Http\Request;
use App\Models\PengembangMateri;
use App\Models\PmAssign;
use App\Models\TextBook;
use Auth;

class ReviewTextBookService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): ReviewTextBookService
    {
        $this->route = $route;

        return $this;
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $pm = pmAssign::where('reviewer_id',$user->id_dosen)
                        ->orWhere('approval_id',$user->id_dosen)
                        ->pluck('id_pm');
        
        $model = PengembangMateri::
            select('pengembang_materi.id_pm', 
                    'semester.nama_semester', 
                    'matakuliah.mk_kode',
                    'matakuliah.mk_nama',
                    'text_book.title',
                    'text_book.kategori',
                    'text_book.tahun'
                   )
            ->whereIn('text_book.status',[0,1,2,3]);

            // Akes untuk assign dan role Reviewer & Approve saja
    
            $thisRole = session()->get('user.dosen')->role_id;
            if ($thisRole == 0 || ($thisRole == 1 && $thisRole != 63)) {
                $model->whereIn('text_book.id_pm',$pm);
            }

            $model->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->join("text_book", "text_book.id_pm", "=", "pengembang_materi.id_pm");
            // ->orderBy("pengembang_materi.status", "asc")
            // ->orderBy("id_pm", "desc");

        return \Table::of($model)
            ->addColumn('status', function ($model) {
                $check = TextBook::where("id_pm", $model->id_pm)->first();
                

                $ret = "Belum diinput";
                if ($check) {
                    $ret = statusCaption($check->status);
                    
                    $userStatus = $this->userStatus($model->id_pm);
                    if ($check->status == 0 && ($userStatus == 'reviewer' || $userStatus == 'approv')) {
                        $ret = $ret." ".ucfirst($userStatus == 'approv' ? 'approver' : $userStatus)." to Approved";
                    }elseif ($check->status == 0) {
                        $ret = $ret." Approval";
                    }
                }

                return $ret;
            })
            ->addColumn('action', function ($model) {
                return \Html::link($this->route . "/detail/" . $model->id_pm, 'View', ["class" => "btn btn-primary btn-sm"]);
            })
            ->make();
    }

    public function ReviewOrApproval(ReviewTextBookRequest $request, $id)
    {
        $user = Auth::user();
        $pengembangMateri = PengembangMateri::with('pm_assign')->findOrFail($id);

        $model = $pengembangMateri->text_book()->first();

        $statusApp = 'approve';

        if ($request->status == 0) {
            $statusApp = 'reject';
        }

        $thisRole = session()->get('user.dosen')->role_id;

        $status = 'dosen';
        
        if ($thisRole == 3 || $thisRole == 63) {
            $status = 'approv';
        }elseif($thisRole == 2){
            $status = 'reviewer';
        }
        // dd($pengembangMateri);
        // check status dosen
        if($pengembangMateri->pm_assign->approval_id == $user->id_dosen || $status == 'approv'){

            $inputs = [
                        'approv_commen' => $request->approv_commen,
                        'approv_date' => date("Y-m-d H:i:s"),
                        'approv_user' => $user->id_dosen
            ];

            if ($request->status == 1) {
                $inputs['status'] = 2;
                
                // send email
            }else{
                $inputs['status'] = 3;

                // send email
            }
        }elseif ($pengembangMateri->pm_assign->reviewer_id == $user->id_dosen || $status == 'reviewer') {

            $inputs = [
                        'reviewer_commen' => $request->reviewer_commen,
                        'reviewer_date' => date("Y-m-d H:i:s"),
                        'reviewer_user' => $user->id_dosen
            ];

            if ($request->status == 1) {
                $inputs['status'] = 1;

                // send email

            }else{
                $inputs['status'] = 3;

                // send email
            }

        }

        $model->update($inputs);
        sendEmail($id,'text-book',$statusApp,$user->id_dosen);
    }

    public function userStatus($id)
    {
        $user = Auth::user();
        $pengembangMateri = PengembangMateri::findOrFail($id);
        $status = false;
        
        $thisRole = session()->get('user.dosen')->role_id;
        
        if ($thisRole ) {
            if ($thisRole == 3 || $thisRole == 63) {
                $status = 'approv';
            }elseif($thisRole == 2){
                $status = 'reviewer';
            }
        }else{
            if ($pengembangMateri->pm_assign->approval_id == $user->id_dosen) {
                $status = 'approv';
            }elseif($pengembangMateri->pm_assign->reviewer_id == $user->id_dosen){
                $status = 'reviewer';
            }
        }

        return $status;
    }

}
