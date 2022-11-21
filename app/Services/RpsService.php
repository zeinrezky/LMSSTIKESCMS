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

class RpsService
{
    private $route;

    public function __construct()
    {
        $this->route = "";
    }

    public function setRoute($route = ""): RpsService
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
            ->where('text_book.status',2);

            // Akes untuk assign dan role SME saja
    
            $thisRole = session()->get('user.dosen')->role_id;
            if ($thisRole == 0 || ($thisRole != 1 && $thisRole != 63)) {
                $model->whereIn('pengembang_materi.id_pm',$pm);
            }

            $model->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->leftJoin("text_book", "text_book.id_pm", "=", "pengembang_materi.id_pm");
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
                $class = "btn btn-primary btn-sm ";
                $name = !empty(@$check->id) ? "Edit" : "Input";

                if (@$check->status == 2 || @$check->status == 1) {
                    // $class .= ' disabled';
                    $name = "View";
                    return \Html::link("review-rps/detail/" . $model->id_pm, $name, ["class" => $class]);
                }

                return \Html::link($this->route . "/detail/" . $model->id_pm, $name, ["class" => $class]);

            })
            ->make();
    }

    public function updateOrcreate(RpsRequest $request, $id)
    {

        $check = Rps::find($id);

        $date = DATE('Y-m-d H:i:s');

        $user = Auth::user();
        
        $save_draft = $request['save_draft'];

        if ($check) {
            // if update    

            $data = Rps::find($id);

            $sendRevisionNotif = false;

            if ($save_draft == 1) {
                $data->status = 4;
            }else{
                if ($data['status'] == 3) {
                    $sendRevisionNotif = true;                
                }
                $data->status = 0;
            }

            $data->strategi_pembelajaran = $request['strategi_pembelajaran'];
            $data->deskripsi_mata_kuliah = $request['deskripsi_mata_kuliah'];
            $data->media_pembelajaran = $request['media_pembelajaran'];
            $data->capaian_pembelajaran = json_encode($request['capaian_pembelajaran'],true);
            $data->metode_penilaian = json_encode($request['mp'],true);
            $data->metode_penilaian_praktikum = json_encode($request['metode_penilaian_praktikum'],true);

            $data->updated_at = $date;

            // upload file

            $peta_kompetensi = $request->file("peta_kompetensi");
            
            if (!empty($peta_kompetensi)) {

                $fileName = $id.'-peta_kompetensi' . "." . $peta_kompetensi->getClientOriginalExtension();

                $peta_kompetensi->storeAs("public/contents/peta_kompetensi/", $fileName);
                $data->peta_kompetensi = $fileName;
            }

            $rubrik_penilaian = $request->file("rubrik_penilaian");
            
            if (!empty($rubrik_penilaian)) {

                $fileName = $id.'-rubrik_penilaian' . "." . $rubrik_penilaian->getClientOriginalExtension();

                $rubrik_penilaian->storeAs("public/contents/rubrik_penilaian/", $fileName);
                $data->rubrik_penilaian = $fileName;
            }

            $data->save();

            // save topic

                // delete topic pm
                Topic::where('id_pm',$id)->delete();
                // insert topic
                if (isset($request['topic'])) {  
                    $payload = [];
                    $i = 0;
                    foreach ($request['topic'] as $key => $v) {
                        $subTopic = $v['sub_topik'];
                        unset($v['sub_topik']);
                        foreach ($subTopic as $subTopickey => $subTopicVal) {
                            $payload[$i] = $v;
                            $payload[$i]['sub_topic'] = $subTopicVal;
                            $payload[$i]['id_pm'] = $id;
                            $payload[$i]['status'] = 0;                       
                           $i++; 
                        }

                    }

                    Topic::insert($payload);
                }

                if ($sendRevisionNotif && $save_draft == 0) {
                    sendEmail($id,'rps','revision',$user->id_dosen);
                }elseif ($data->status == 0 && $save_draft == 0) {
                    // send email create
                    sendEmail($id,'rps','input',$user->id_dosen);
                }
                return true;
        }else{
            // if create

            // save RPS
            $payload = [
                'id' => $id,
                'strategi_pembelajaran' => $request['strategi_pembelajaran'],
                'deskripsi_mata_kuliah' => $request['deskripsi_mata_kuliah'],
                'media_pembelajaran' => $request['media_pembelajaran'],
                'capaian_pembelajaran' => json_encode($request['capaian_pembelajaran'],true),
                'metode_penilaian' => json_encode($request['mp'],true),
                'metode_penilaian_praktikum' => json_encode($request['metode_penilaian_praktikum'],true),
                'created_at' => $date,
                'updated_at' => $date
            ];

            if ($save_draft == 1) {
                $payload["status"] = 4;
            }else{
                $payload["status"] = 0;
            }
            

            // upload file

            $peta_kompetensi = $request->file("peta_kompetensi");
            
            if (!empty($peta_kompetensi)) {

                $fileName = $id.'-peta_kompetensi' . "." . $peta_kompetensi->getClientOriginalExtension();

                $peta_kompetensi->storeAs("public/contents/peta_kompetensi/", $fileName);
                $payload['peta_kompetensi'] =  $fileName;
            }

            $rubrik_penilaian = $request->file("rubrik_penilaian");
            
            if (!empty($rubrik_penilaian)) {

                $fileName = $id.'-rubrik_penilaian' . "." . $rubrik_penilaian->getClientOriginalExtension();

                $rubrik_penilaian->storeAs("public/contents/rubrik_penilaian/", $fileName);
                $payload['rubrik_penilaian'] =  $fileName;
            }

            $save = Rps::insert($payload);

            // insert topic
            if (isset($request['topic'])) {            
                $payload = [];
                $i = 0;
                foreach ($request['topic'] as $key => $v) {

                    $subTopic = $v['sub_topik'];
                    unset($v['sub_topik']);
                    foreach ($subTopic as $subTopickey => $subTopicVal) {
                        $payload[$i] = $v;
                        $payload[$i]['sub_topic'] = $subTopicVal;
                        $payload[$i]['id_pm'] = $id;
                        $payload[$i]['status'] = 0;                       
                       $i++; 
                    }

                }

                Topic::insert($payload);
            }

            if ($save_draft == 0) {
                // send email
                sendEmail($id,'rps','input',$user->id_dosen);
            }

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
