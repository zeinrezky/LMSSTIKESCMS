<?php

namespace App\Services;

use App\Models\PmAssign;
use Illuminate\Support\Str;
use App\Singleton\Component;
use Illuminate\Http\Request;
use App\Models\PengembangMateri;
use App\Models\OrModel;
use App\Models\OrFileModel;
use App\Models\Topic;
use App\Models\TextBook;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrRequest;
use Auth;

class ReviewOrService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): ReviewOrService
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
            ->where('text_book.status',2)
            ->where('or.status','!=',4)
            ->where('rps.status',2);
            
            // Akes untuk assign dan role Reviewer & Approve saja
    
            $thisRole = session()->get('user.dosen')->role_id;
            if ($thisRole == 0 || ($thisRole == 1 && $thisRole != 63)) {
                $model->whereIn('pengembang_materi.id_pm',$pm);
            }

            $model->join("rps", "rps.id", "=", "pengembang_materi.id_pm")
            ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->leftJoin("text_book", "text_book.id_pm", "=", "pengembang_materi.id_pm")
            ->join("or", "or.id", "=", "pengembang_materi.id_pm");
            // ->orderBy("pengembang_materi.status", "desc")
            // ->orderBy("id_pm", "desc");

        return \Table::of($model)
            ->addColumn('status', function ($model) {
                $check = OrModel::where("id", $model->id_pm)->first();
                $ret = "Or Belum diinput";
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

                $check = OrModel::where("id", $model->id_pm)->first();
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

    public function reviewOrApprove(OrRequest $request, $id)
    {

        $check = OrModel::find($id);

        $date = DATE('Y-m-d H:i:s');
        
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

        if ($check) {

            // if review / approv

            // if (isset($request->reviewer_commen) || isset($request->approv_commen)) {

                $user = Auth::user();
                $pengembangMateri = PengembangMateri::with('pm_assign')->findOrFail($id);
                $model = $pengembangMateri->or()->first();

                // check status dosen
                if($pengembangMateri->pm_assign->approval_id == $user->id_dosen || $status == 'approv'){

                    $inputs = [
                                'approv_commen' => $request->approv_commen,
                                'approv_date' => date("Y-m-d H:i:s"),
                                'approv_user' => $user->id_dosen
                    ];

                    if ($request->status == 1) {
                        $inputs['status'] = 2;
                    }else{
                        $inputs['status'] = 3;
                    }
                }elseif ($pengembangMateri->pm_assign->reviewer_id == $user->id_dosen || $status == 'reviewer') {

                    $inputs = [
                                'reviewer_commen' => $request->reviewer_commen,
                                'reviewer_date' => date("Y-m-d H:i:s"),
                                'reviewer_user' => $user->id_dosen
                    ];

                    if ($request->status == 1) {
                        $inputs['status'] = 1;
                    }else{
                        $inputs['status'] = 3;
                    }

                }
                
                $model->update($inputs);
                
                sendEmail($id,'or',$statusApp,$user->id_dosen);
                
                return true;
            // }

        }else{
            // if create

            // save Or

            $payload = [
                'id' => $id,
                'status' => 0,
                'created_at' => $date,
                'updated_at' => $date
            ];
            // dd($request->all());
            // upload file

            // PPT
                if (isset($request->ppt)) {
                    $fileData = [];
                    foreach ($request->ppt as $key => $v) {
                        
                        $file = $v['file'];

                        $fileName = $id.'-'. generateRandomString(5) . "." . $file->getClientOriginalExtension();

                        $file->storeAs('public/contents/or_ppt/', $fileName);
                        
                        $fileData[$key] = [
                                            'topic_id' => $v['topic_id'],
                                            'id_pm' => $id,
                                            'type' => 'or_ppt',
                                            'file' => $fileName,
                                          ];
                    }

                    OrFileModel::insert($fileData);
                }
            // LN
                if (isset($request->ln)) {
                    $fileData = [];
                    foreach ($request->ln as $key => $v) {
                        
                        $file = $v['file'];

                        $fileName = $id.'-'. generateRandomString(5) . "." . $file->getClientOriginalExtension();

                        $file->storeAs('public/contents/or_ln/', $fileName);
                        
                        $fileData[$key] = [
                                            'topic_id' => $v['topic_id'],
                                            'id_pm' => $id,
                                            'type' => 'or_ln',
                                            'file' => $fileName,
                                          ];
                    }

                    OrFileModel::insert($fileData);
                }

            // VIDEO

                if (isset($request->video)) {
                    $fileData = [];
                    foreach ($request->video as $key => $v) {
                        
                        $file = $v['file'];

                        $fileName = $id.'-'. generateRandomString(5) . "." . $file->getClientOriginalExtension();

                        $file->storeAs('public/contents/or_video/', $fileName);
                        
                        $fileData[$key] = [
                                            'topic_id' => $v['topic_id'],
                                            'id_pm' => $id,
                                            'type' => 'or_video',
                                            'file' => $fileName,
                                          ];
                    }

                    OrFileModel::insert($fileData);
                }

            // Materi Pendukung
                if (isset($request->materi_pendukung)) {
                    $fileData = [];
                    foreach ($request->materi_pendukung as $key => $v) {
                        
                        $fileName = '';

                        if (isset($v['file'])) {
                            $file = $v['file'];
                            $fileName = $id.'-'. generateRandomString(5) . "." . $file->getClientOriginalExtension();

                            $file->storeAs('public/contents/or_materi_pendukung/', $fileName);
                        }
                        
                        $fileData[$key] = [
                                            'id_pm' => $id,
                                            'topic_id' => $v['topic_id'],
                                            'title' => $v['title'],
                                            'link' => $v['link'],
                                            'type' => 'or_materi_pendukung',
                                            'file' => $fileName,
                                          ];
                    }

                    OrFileModel::insert($fileData);
                }

            $save = OrModel::insert($payload);

            return true;
        }

    }
}
