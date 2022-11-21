<?php

namespace App\Http\Controllers;

use App\Models\PengembangMateri;
use App\Models\Rps;
use App\Models\TextBook;
use App\Models\Topic;
use App\Models\MetodePenilaian;
use App\Services\ReviewRpsService;
use Illuminate\Http\Request;
use App\Http\Requests\RpsRequest;

class ReviewRpsController extends Controller
{
    public function __construct(PengembangMateri $pengembangMateri, TextBook $textBook, ReviewRpsService $ReviewRpsService)
    {
        $this->pengembangMateri = $pengembangMateri;
        $this->__route = "review-rps";
        $this->service = (new ReviewRpsService())->setRoute("review-rps");
        $this->textBook = $textBook;
        $this->ReviewRpsService = $ReviewRpsService;

        view()->share("__route", $this->__route);
        view()->share("__menu", "RPS");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("rps.index");
    }

    public function getDetail($id)
    {

        $pm = PengembangMateri::
            select('pengembang_materi.id_pm', 
                    'semester.nama_semester', 
                    'matakuliah.mk_kode',
                    'matakuliah.mk_nama'
                   )
            ->where('pengembang_materi.id_pm',$id)
            ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
            ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
            ->first();

        $titleAction = $pm->nama_semester." • ".$pm->mk_kode." • ".$pm->mk_nama;
        $textBook = $this->textBook->where('id_pm',$id)->first();
        $rps = Rps::find($id);
        $rps = Rps::find($id);
        
        $metodePenilaianArr = MetodePenilaian::select('id','component','category')->get();
        
        $metodePenilaian = [];

        foreach ($metodePenilaianArr as $key => $v) {
            $metodePenilaian[$v['category']][$v['id']] = $v;
        }

        $capaianPembelajaran = [];
        $topic = [];
        $metodePenilaianData = [];
        $totalSubTopic = 0;
        if ($rps) {
            $capaianPembelajaran = json_decode($rps['capaian_pembelajaran'],true); 
            $topicArr = Topic::where('id_pm',$id)->get();
            $totalSubTopic = $topicArr->count(); 
            
            $metodePenilaianData = json_decode($rps['metode_penilaian'],true);

            foreach ($topicArr as $key => $v) {
                // key selected
                $keyCP = 0;
                foreach ($capaianPembelajaran as $keyC => $vC) {
                    if (strtolower(str_replace(' ', '', $vC)) == strtolower(str_replace(' ', '', $v['capaian_pembelajaran']))) {
                        $keyCP = $keyC;
                        break;
                    }
                }
                $topic[$v['topic']][] = [
                                            'sesi' => $v['sesi'],
                                            'sub_topic' => $v['sub_topic'],
                                            'capaian_pembelajaran' => $v['capaian_pembelajaran'],
                                            'key_cp' => $keyCP,
                                        ];
            }

        }

        $userStatus = $this->service->userStatus($id);
        $thisRole = session()->get('user.dosen')->role_id;
        
        return view("rps.form", [
            "model" => $textBook,
            "metodePenilaian" => $metodePenilaian,
            "metodePenilaianData" => $metodePenilaianData,
            "capaianPembelajaran" => $capaianPembelajaran,
            "totalSubTopic" => $totalSubTopic,
            "topic" => $topic,
            "thisRole" => $thisRole,
            "userStatus" => $userStatus,
            "review_stat" => true,
            "titleAction" => $titleAction,
            "rps" => $rps,
        ]);
    }

    public function postDetail(RpsRequest $request, $id)
    {
        $this->ReviewRpsService->ReviewOrApproval($request, $id);
        toast('Data telah diupdate', 'success');

        return redirect($this->__route);
    }

    public function viewPdf($type, $file)
    {
        $pathToFile = Storage::url(contents_path().$type.'/'.$file);
        return Storage::response($pathToFile);
    }
}
