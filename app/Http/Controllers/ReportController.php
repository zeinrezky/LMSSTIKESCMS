<?php

namespace App\Http\Controllers;

use App\Models\PengembangMateri;
use App\Models\TextBook;
use App\Models\Semester;
use App\Models\MataKuliah;
use App\Models\Rps;
use App\Models\OrModel;
use App\Models\OrFileModel;
use App\Models\Topic;
use App\Models\Latihan;
use App\Models\Kuis;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Storage;
use DB;

class ReportController extends Controller
{
    public function __construct(PengembangMateri $pengembangMateri, TextBook $textBook, ReportService $ReportService)
    {
        $this->pengembangMateri = $pengembangMateri;
        $this->__route = "report";
        $this->service = (new ReportService())->setRoute("report");
        $this->textBook = $textBook;
        $this->ReportService = $ReportService;

        view()->share("__route", $this->__route);
        view()->share("__menu", "Report");
    }

    public function kemajuanPerkembanganData(Request $request)
    {
        return $this->service->getDataKP($request);
    }

    public function statusSilabusData(Request $request)
    {
        return $this->service->getDataSS($request);
    }

    public function kemajuanPerkembangan()
    {
        return view('report.kemajuan_perkembangan.index');
    }

    public function kemajuanPerkembanganDetail($id_semester, $id_matakuliah)
    {
        $semester = Semester::find($id_semester)['nama_semester'];
        $mataKuliah = MataKuliah::find($id_matakuliah)['mk_nama'];

        $title = $semester.' • '.$mataKuliah;

        //report
        $pm = PengembangMateri::where('pengembang_materi.id_semester',$id_semester)
                                  ->where('pengembang_materi.id_matakuliah',$id_matakuliah)
                                  ->selectRaw('pengembang_materi.id_pm,nama_semester,mk_nama,

                                        sme.nama as sme_nama,
                                        sme.nip as sme_nip,
                                        sme.email as sme_email,

                                        CONCAT(sme.nip," | ",sme.nama) as sme,
                                        CONCAT(rev.nip," | ",rev.nama) as reviewer,
                                        CONCAT(app.nip," | ",app.nama) as approver'
                                        )
                                ->leftJoin('pm_assign','pengembang_materi.id_pm','=','pm_assign.id_pm')
                                ->leftJoin('dosen as sme','sme.id_dosen','=','pm_assign.sme_id')
                                ->leftJoin('dosen as rev','rev.id_dosen','=','pm_assign.reviewer_id')
                                ->leftJoin('dosen as app','app.id_dosen','=','pm_assign.approval_id')
                                ->join("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
                                ->join("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
                                ->get();

        $pm_id = $pm->pluck('id_pm');
        // textbook
        $textbook = TextBook::whereIn('id_pm',$pm_id)
                              ->select('id_pm as id','status')
                              ->get()
                              ->pluck('status','id');
        // rps
        $rps = Rps::whereIn('id',$pm_id)
                              ->select('id','status')
                              ->get()
                              ->pluck('status','id');

        // or
        $or = OrModel::whereIn('id',$pm_id)
                              ->select('id','status')
                              ->get()
                              ->pluck('status','id');
        $report = [];
        // dd($or);
        foreach ($pm as $key => $v) {
            $report[$key] = $v;
            $report[$key]['textbook'] = (isset($textbook[(int)$v['id_pm']]) ? $textbook[(int)$v['id_pm']] : false);
            $report[$key]['rps'] = (isset($rps[(int)$v['id_pm']]) ? $rps[(int)$v['id_pm']] : false);
            $report[$key]['or'] = (isset($or[(int)$v['id_pm']]) ? $or[(int)$v['id_pm']] : false);

        }
        // dd($report);
        return view('report.kemajuan_perkembangan.detail')
                    ->with('title',$title)
                    ->with('report',$report);
    }

    public function statusSilabus()
    {
        return view('report.status_silabus.index');
    }

    public function statusSilabusDetail($id_semester, $id_matakuliah)
    {
        $semester = Semester::find($id_semester)['nama_semester'];
        $mataKuliah = MataKuliah::find($id_matakuliah)['mk_nama'];

        $title = $semester.' • '.$mataKuliah;

        //report
        $pm_id = PengembangMateri::where('id_semester',$id_semester)
                                  ->where('id_matakuliah',$id_matakuliah)
                                  ->pluck('id_pm');

        $mediaPembelajaranAV = Rps::whereIn('id',$pm_id)
                    ->select(
                             'id',
                             'media_pembelajaran'
                            )
                    ->where('media_pembelajaran','!=','')
                    ->pluck('id','id');

        $CPAV = Rps::whereIn('id',$pm_id)
                    ->select(
                             'id',
                             'capaian_pembelajaran'
                            )
                    ->where('capaian_pembelajaran','!=','')
                    ->pluck('id','id');

        $topic = Topic::whereIn('topic.id_pm',$pm_id)
                        ->leftJoin("text_book", "text_book.id_pm", "=", "topic.id_pm")
                        ->select('topic.*','text_book.title as text_book')
                        ->groupBy('topic')
                        // ->orderBy('sesi')
                        ->orderByRaw('CONVERT(sesi, SIGNED) asc')
                        ->get();

        $subTopicAv = Topic::whereIn('id_pm',$pm_id)
                        ->where('sub_topic','!=','')
                        ->selectRaw('id_topic')
                        ->get()
                        ->pluck('id_topic','id_topic')
                        ->toArray();

        $orFileAV = OrFileModel::whereIn('id_pm',$pm_id)
                        ->get()
                        ->pluck('topic_id','topic_id')
                        ->toArray();

        // Peta Kompetensi, 
        $peta_kompetensi = Rps::whereIn('id',$pm_id)
                    ->select(
                             'id',
                             'peta_kompetensi'
                            )
                    ->where('peta_kompetensi','!=','')
                    ->pluck('id','id');

        // Rubrik Penilaian, 
        $rubrik_penilaian = Rps::whereIn('id',$pm_id)
                    ->select(
                             'id',
                             'rubrik_penilaian'
                            )
                    ->where('rubrik_penilaian','!=','')
                    ->pluck('id','id');

        // PPT, 
        $orFilePPT = OrFileModel::whereIn('id_pm',$pm_id)
                        ->where('type','or_ppt')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();

        // LN, 
        $orFileLN = OrFileModel::whereIn('id_pm',$pm_id)
                        ->where('type','or_ln')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();

        // Video, 
        $orFileVideo = OrFileModel::whereIn('id_pm',$pm_id)
                        ->where('type','or_video')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();

        // Materi Pendukung, 
        $orFileMateriPendukung = OrFileModel::whereIn('id_pm',$pm_id)
                        ->where('type','or_materi_pendukung')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();

        // Exercise, 
        $Exercise = Latihan::whereIn('id_pm',$pm_id)
                        ->groupBy('id_pm')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();


        // Kuis
        $Kuis = Kuis::whereIn('id_pm',$pm_id)
                        ->groupBy('id_pm')
                        ->get()
                        ->pluck('id_pm','id_pm')
                        ->toArray();

        $report = [];
        foreach ($topic as $key => $v) {
            $report[$key]['id_topic'] = $v['id_topic'];
            $report[$key]['sesi'] = $v['sesi'];
            $report[$key]['text_book'] = $v['text_book'];
            $report[$key]['topic'] = $v['topic'];
            $report[$key]['sub_topic'] = (isset($subTopicAv[(int)$v['id_topic']]) ? 1 : 0);
            $report[$key]['media_keterangan'] = (isset($orFileAV[(int)$v['id_topic']]) ? 1 : 0);
            $report[$key]['media_pembelajaran'] = (isset($mediaPembelajaranAV[(int)$v['id_pm']]) ? 1 : 0);
            $report[$key]['cp'] = (isset($CPAV[(int)$v['id_pm']]) ? 1 : 0);

            // Peta Kompetensi, 
            $report[$key]['peta_kompetensi'] = (isset($peta_kompetensi[(int)$v['id_pm']]) ? 1 : 0);

            // Rubrik Penilaian, 
            $report[$key]['rubrik_penilaian'] = (isset($rubrik_penilaian[(int)$v['id_pm']]) ? 1 : 0);
            
            // PPT, 
            $report[$key]['orFilePPT'] = (isset($orFilePPT[(int)$v['id_pm']]) ? 1 : 0);
            
            // LN, 
            $report[$key]['orFileLN'] = (isset($orFileLN[(int)$v['id_pm']]) ? 1 : 0);
            
            // Video, 
            $report[$key]['orFileVideo'] = (isset($orFileVideo[(int)$v['id_pm']]) ? 1 : 0);
            
            // Materi Pendukung, 
            $report[$key]['orFileMateriPendukung'] = (isset($orFileMateriPendukung[(int)$v['id_pm']]) ? 1 : 0);
            
            // Exercise, 
            $report[$key]['exercise'] = (isset($Exercise[(int)$v['id_pm']]) ? 1 : 0);
            
            // Kuis
            $report[$key]['kuis'] = (isset($Kuis[(int)$v['id_pm']]) ? 1 : 0);
            
        }

        return view('report.status_silabus.detail')
                    ->with('title',$title)
                    ->with('report',$report);
    }
}