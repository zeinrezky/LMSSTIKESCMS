<?php

namespace App\Services;

use App\Models\PmAssign;
use Illuminate\Support\Str;
use App\Singleton\Component;
use Illuminate\Http\Request;
use App\Models\PengembangMateri;
use App\Models\Rps;
use App\Models\Topic;
use App\Models\TextBook;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RpsRequest;
use Auth;

class ReviewRpsService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): ReviewRpsService
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
            ->where('text_book.status',2);
            
            // Akes untuk assign dan role Reviewer & Approve saja
    
            $thisRole = session()->get('user.dosen')->role_id;
            if ($thisRole == 0 || ($thisRole == 1 && $thisRole != 63)) {
                $model->whereIn('pengembang_materi.id_pm',$pm);
            }

            $model->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->leftJoin("text_book", "text_book.id_pm", "=", "pengembang_materi.id_pm")
            ->join("rps", "rps.id", "=", "pengembang_materi.id_pm");
            // ->orderBy("pengembang_materi.status", "desc")
            // ->orderBy("id_pm", "desc");

        return \Table::of($model)
            ->addColumn('status', function ($model) {
                $check = Rps::where("id", $model->id_pm)->first();
                $ret = "RPS Belum diinput";
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
                $check = Rps::where("id", $model->id_pm)->first();
                $name = !empty(@$check) ? "View" : "Input";
                
                return \Html::link($this->route . "/detail/" . $model->id_pm, $name, ["class" => "btn btn-primary"]);
            })
            ->make();
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

    public function ReviewOrApproval(RpsRequest $request, $id)
    {
        $user = Auth::user();
        $pengembangMateri = PengembangMateri::with('pm_assign')->findOrFail($id);
        $model = $pengembangMateri->rps()->first();

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
        sendEmail($id,'rps',$statusApp,$user->id_dosen);
    }
}
