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

class OrService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): OrService
    {
        $this->route = $route;

        return $this;
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $pm = pmAssign::where('sme_id',$user->id_dosen)->pluck('id_pm');
        
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
            ->where('rps.status',2);
            
            // Akes untuk assign dan role SME saja
    
            $thisRole = session()->get('user.dosen')->role_id;
            if ($thisRole == 0 || ($thisRole != 1 && $thisRole != 63)) {
                $model->whereIn('pengembang_materi.id_pm',$pm);
            }

            $model->join("rps", "rps.id", "=", "pengembang_materi.id_pm")
            ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->leftJoin("text_book", "text_book.id_pm", "=", "pengembang_materi.id_pm");
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
                $class = "btn btn-primary btn-sm ";
                $name = !empty(@$check->id) ? "Edit" : "Input";

                if (@$check->status == 2 || @$check->status == 1) {
                    // $class .= ' disabled';
                    $name = "View";
                    return \Html::link("review-or/detail/" . $model->id_pm, $name, ["class" => $class]);
                }

                return \Html::link($this->route . "/detail/" . $model->id_pm, $name, ["class" => $class]);

            })
            ->make();
    }

    public function updateOrcreate(OrRequest $request, $id)
    {

        $check = OrModel::find($id);

        $date = DATE('Y-m-d H:i:s');
        $user = Auth::user();

        if ($check) {

            $sendRevisionNotif = false;

            if ($check['status'] == 3) {
                $sendRevisionNotif = true;                
            }
            // if review / approve

            // update File    

            // PPT
                if (isset($request->old_ppt)) {

                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_ppt')
                                 ->whereNotIn('id',$request->old_ppt)
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_ppt/'.$v->file));
                    }

                    $delete->delete();
                }else{
                    // delete all
                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_ppt')
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_ppt/'.$v->file));
                    }

                    $delete->delete();
                }

            // LN
                if (isset($request->old_ln)) {

                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_ln')
                                 ->whereNotIn('id',$request->old_ln)
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_ln/'.$v->file));
                    }

                    $delete->delete();
                }else{
                    // delete all
                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_ln')
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_ln/'.$v->file));
                    }

                    $delete->delete();
                }

            // Video
                if (isset($request->old_video)) {

                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_video')
                                 ->whereNotIn('id',$request->old_video)
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_video/'.$v->file));
                    }

                    $delete->delete();
                }else{
                    // delete all
                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_video')
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_video/'.$v->file));
                    }

                    $delete->delete();
                }

            // Materi Pendukung
                if (isset($request->old_materi_pendukung)) {

                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_materi_pendukung')
                                 ->whereNotIn('id',$request->old_materi_pendukung)
                                 ->select('file');
                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_materi_pendukung/'.$v->file));
                    }

                    $delete->delete();
                }else{
                    // delete all
                    $delete = OrFileModel::where('id_pm',$id)
                                 ->where('type','or_materi_pendukung')
                                 ->select('file');

                    foreach ($delete->get() as $key => $v) {
                        \Storage::delete(contents_path('or_materi_pendukung/'.$v->file));
                    }

                    $delete->delete();
                }

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

                    OrFileModel::insert(array_values($fileData));
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

                    OrFileModel::insert(array_values($fileData));
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

                    OrFileModel::insert(array_values($fileData));
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
                                            'source' => $v['source'],
                                            'type' => 'or_materi_pendukung',
                                            'file' => $fileName,
                                          ];
                    }

                    OrFileModel::insert(array_values($fileData));
                }

                $check->status = 0;
                $check->save();
                
                if ($sendRevisionNotif) {
                    sendEmail($id,'or','revision',$user->id_dosen);
                }

                return true;
        }else{
            // if create

            // save Or

            $payload = [
                'id' => $id,
                'status' => 0,
                'created_at' => $date,
                'updated_at' => $date
            ];
            
            // upload file

            // PPT
                if (isset($request->ppt)) {
                    $fileData = [];
                    foreach ($request->ppt as $key => $v) {
                        
                        if (!isset($v['file'])) {
                            continue;
                        }

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

                    OrFileModel::insert(array_values($fileData));
                }

            // LN
                if (isset($request->ln)) {
                    $fileData = [];
                    foreach ($request->ln as $key => $v) {
                        
                        if (!isset($v['file'])) {
                            continue;
                        }

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

                    OrFileModel::insert(array_values($fileData));
                }

            // VIDEO

                if (isset($request->video)) {
                    $fileData = [];
                    foreach ($request->video as $key => $v) {
                        
                        if (!isset($v['file'])) {
                            continue;
                        }

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

                    OrFileModel::insert(array_values($fileData));
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
                                            'source' => $v['source'],
                                            'type' => 'or_materi_pendukung',
                                            'file' => $fileName,
                                          ];
                    }
                    
                    OrFileModel::insert(array_values($fileData));
                }

            $save = OrModel::insert($payload);

            // send email
            $user = Auth::user();
            sendEmail($id,'or','input',$user->id_dosen);

            return true;
        }

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
