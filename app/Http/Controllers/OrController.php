<?php

namespace App\Http\Controllers;

use App\Models\PengembangMateri;
use App\Models\OrModel;
use App\Models\OrFileModel;
use App\Models\TextBook;
use App\Models\Topic;
use App\Models\Kuis;
use App\Models\Latihan;
use App\Models\MetodePenilaian;
use App\Services\OrService;
use Illuminate\Http\Request;
use App\Http\Requests\OrRequest;
use Storage;

class OrController extends Controller
{
    public function __construct(PengembangMateri $pengembangMateri, TextBook $textBook, OrService $OrService)
    {
        $this->pengembangMateri = $pengembangMateri;
        $this->__route = "or";
        $this->service = (new OrService())->setRoute("or");
        $this->textBook = $textBook;
        $this->OrService = $OrService;

        view()->share("__route", $this->__route);
        view()->share("__menu", "OR");
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function getIndex()
    {
        return view("or.index");
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
        $or = OrModel::find($id);
        $metodePenilaian = MetodePenilaian::get();

        $metodePenilaianChecked = [];
        $capaianPembelajaran = [];
        $topic = [];
        $totalSubTopic = 0;
        $topic = Topic::where('id_pm',$id)
                            ->selectRaw('id_topic, sesi, CONCAT("Sesi : ",sesi," • Topik : ",topic," • Sub Topik : ",sub_topic) as topic')
                            ->orderBy('sesi')
                            // ->groupBy('topic')
                            ->orderBy('topic')
                            ->get();
        $orFile = [];
        $orFileData = OrFileModel::where('id_pm',$id)->get();
        if ($orFileData->count() > 0) {

            foreach ($orFileData as $key => $v) {
                $orFile[$v->type] = $orFileData->where('type',$v->type);
            }
        }

        return view("or.form", [
            "model" => $textBook,
            "metodePenilaianChecked" => $metodePenilaianChecked,
            "capaianPembelajaran" => $capaianPembelajaran,
            "metodePenilaian" => $metodePenilaian,
            "totalSubTopic" => $totalSubTopic,
            "topic" => $topic,
            "orFile" => $orFile,
            "titleAction" => $titleAction,
            "or" => $or,
            "review_stat" => false,
        ]);
    }

    public function postDetail(OrRequest $request, $id)
    {
        $this->OrService->updateOrcreate($request, $id);
        toast('Data telah diupdate', 'success');

        return redirect($this->__route);
    }

    public function viewPdf($type, $file)
    {
        $pathToFile = Storage::url(contents_path().$type.'/'.$file);
        return Storage::response($pathToFile);
    }

    // KUIS

    public function updateQuestion(Request $request, $id)
    {

        $payload = [
            'durasi' => $request['durasi'],
            'isi_soal' => $request['isi_soal'],
            'jawaban' => $request['jawaban'],
            'pilihan_a' => $request['pilihan_a'],
            'pilihan_b' => $request['pilihan_b'],
            'pilihan_c' => $request['pilihan_c'],
            'pilihan_d' => $request['pilihan_d'],
            'penjelasan_jwb' => $request['penjelasan_jwb'],
            'id_topic' => $request['id_topic'],
            'varian_latihan' => $request['varian_latihan']
        ];

        $kuis = Kuis::where('id_kuis',$request->question_id)->update($payload);

        return response()->json($kuis);
    }

    public function question($id)
    {

        $pmStatus = PengembangMateri::select('status')->where('pengembang_materi.id_pm',$id)->first();

        $or = OrModel::select('status')->where('id',$id)->first();
        
        $kuis = Kuis::where('kuis.id_pm',$id)
                      ->select(
                                'kuis.id_kuis',
                                'kuis.durasi',
                                'kuis.isi_soal',
                                'kuis.jawaban',
                                'kuis.pilihan_a',
                                'kuis.pilihan_b',
                                'kuis.pilihan_c',
                                'kuis.pilihan_d',
                                'kuis.penjelasan_jwb',
                                'kuis.varian_latihan',
                                'kuis.id_topic',
                                'topic.topic',
                                'topic.sesi',
                                'topic.sub_topic'
                               )
                      ->leftJoin('topic','topic.id_topic','=','kuis.id_topic')
                      ->orderBy('varian_latihan','ASC')
                      ->orderBy('kuis.id_kuis','ASC')
                      ->get();

        $html_tabel = '<tr><td colspan="4" class="text-center"><strong>Belum ada soal</strong></td></td>';
        
        $html_variant = '<select class="form-control" id="varian_latihan">';

        $kuisData = [];
        
        if ($kuis->count() > 0) {
            $kuisArr = [];
            foreach ($kuis as $key => $v) {
                $kuisArr[$v['varian_latihan']] = array_values($kuis->where('varian_latihan',$v['varian_latihan'])->toArray());
                $kuisData[$v['id_kuis']] = $v;
            }

            $html_tabel = '';

                foreach ($kuisArr as $keyV => $v) {
                    $html_tabel .= '<tr>';
                        $html_tabel .= '<td class="text-center" rowspan="'.(count($v)+1).'">';
                            $html_tabel .= '<strong>'.$keyV.'</strong>';
                        $html_tabel .= '</td>';
                    $html_tabel .= '</tr>';

                    $no = 1;
                    $alphabet = ['a','b','c','d'];
                    foreach ($v as $key => $q) {
                        $html_tabel .= '<tr>';
                            $html_tabel .= '<td class="text-center">'.$no++.'</td>';
                            $html_tabel .= '<td>';
                                $html_tabel .= '<div class="row">';
                                    $html_tabel .= '<div class="col-md-12">';
                                        $html_tabel .= '<h4><b>Soal :</b></h4>';
                                        $html_tabel .= $q['isi_soal'];
                                    $html_tabel .= '</div>';

                                    $html_tabel .= '<div class="col-md-12">';
                                        $html_tabel .= '<h4><b>Pilihan :</b></h4>';
                                        
                                        foreach ($alphabet as $a) {
                                            if (!$q['pilihan_'.$a]) {
                                                continue;
                                            }
                                            $html_tabel .= '<div class="row">';
                                                $html_tabel .= '<div class="col-md-1 text-center">';
                                                    $html_tabel .= '<strong>'.ucfirst($a.'.').'</strong>';
                                                $html_tabel .= '</div>';
                                                $html_tabel .= '<div class="col-md-11 pl-0">';
                                                    $html_tabel .= $q['pilihan_'.$a];
                                                $html_tabel .= '</div>';
                                            $html_tabel .= '</div>';
                                        }

                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h4><b>Jawaban : '.$q['jawaban'].'</b></h4>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                        $html_tabel .= '<div class="row mb-3">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h4><b>Explanation :</b></h4>';
                                                $html_tabel .= $q['penjelasan_jwb'] ?? '-';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Sesi : </b>'.$q['sesi'].'</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';
                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Topic : </b>'.$q['topic'].' | '.$q['sub_topic'].'</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';
                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Durasi : </b>'.$q['durasi'].' menit</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                    $html_tabel .= '</div>';
                                    
                                $html_tabel .= '</div>';
                            $html_tabel .= '</td>';

                            if (!$or || ($or['status'] == 0 || $or['status'] == 3)) {
                                $html_tabel .= '<td width="10%" class="text-center">';
                                    $html_tabel .= '<span onclick="editQ('.$q['id_kuis'].')" class="btn btn-success mr-2 mb-2"><i class="fa fa-edit"></i></span>';
                                    $html_tabel .= '<span class="btn btn-danger mb-2" 
                                                    onclick="delQ('.$q['id_kuis'].')"><i class="fa fa-trash"></i></span>';
                                $html_tabel .= '</td>';
                            }

                        $html_tabel .= '</tr>';
                    }
                }
        }
        
        if ($kuis->count() > 0) {

                for ($i=1; $i <= ($kuis->max('varian_latihan') + 1); $i++) {
                    if (($kuis->max('varian_latihan') + 1) == $i) {
                        $html_variant .= '<option selected>'.$i.'</option>';
                     } else{
                        $html_variant .= '<option>'.$i.'</option>';
                     }
                }

        }else{
            $html_variant .= '<option selected>1</option>';
        }
        
        $html_variant .= '</select>';

        $ret = [
            'html_tabel_kuis' => $html_tabel,
            'kuis_data' => $kuisData,
            'html_varian_kuis' => $html_variant
        ];
        
        return response()->json($ret);
    }

    public function questionStore(Request $request, $id)
    {
        $request['id_pm'] = $id;
        $save = Kuis::insert($request->all());
        return response()->json($save);
    }

    public function deleteQuestion(Request $request, $id)
    {
        $delete = Kuis::where('id_kuis',$request->id)->delete();

        return response()->json($delete);
    }

    // LATIHAN

    public function updateQuestionExercise(Request $request, $id)
    {

        $payload = [
            'durasi' => $request['durasi'],
            'isi_soal' => $request['isi_soal'],
            'jawaban' => $request['jawaban'],
            'pilihan_a' => $request['pilihan_a'],
            'pilihan_b' => $request['pilihan_b'],
            'pilihan_c' => $request['pilihan_c'],
            'pilihan_d' => $request['pilihan_d'],
            'penjelasan_jwb' => $request['penjelasan_jwb'],
            'id_topic' => $request['id_topic'],
            'varian_latihan' => $request['varian_latihan']
        ];

        $latihan = Latihan::where('id_lat',$request->question_id)->update($payload);

        return response()->json($latihan);
    }

    public function questionExercise($id)
    {

        $pmStatus = PengembangMateri::select('status')->where('pengembang_materi.id_pm',$id)->first();

        $or = OrModel::select('status')->where('id',$id)->first();
        
        $latihan = Latihan::where('latihan.id_pm',$id)
                      ->select(
                                'latihan.id_lat',
                                'latihan.durasi',
                                'latihan.isi_soal',
                                'latihan.jawaban',
                                'latihan.pilihan_a',
                                'latihan.pilihan_b',
                                'latihan.pilihan_c',
                                'latihan.pilihan_d',
                                'latihan.penjelasan_jwb',
                                'latihan.varian_latihan',
                                'latihan.id_topic',
                                'topic.sesi',
                                'topic.topic',
                                'topic.sub_topic'
                               )
                      ->leftJoin('topic','topic.id_topic','=','latihan.id_topic')
                      ->orderBy('varian_latihan','ASC')
                      ->orderBy('latihan.id_lat','ASC')
                      ->get();

        $html_tabel = '<tr><td colspan="4" class="text-center"><strong>Belum ada soal</strong></td></td>';
        
        $html_variant = '<select class="form-control" id="exercise_varian_latihan">';

        $latihanData = [];
        
        if ($latihan->count() > 0) {
            $latihanArr = [];
            foreach ($latihan as $key => $v) {
                $latihanArr[$v['varian_latihan']] = array_values($latihan->where('varian_latihan',$v['varian_latihan'])->toArray());
                $latihanData[$v['id_lat']] = $v;
            }

            $html_tabel = '';

                foreach ($latihanArr as $keyV => $v) {
                    $html_tabel .= '<tr>';
                        $html_tabel .= '<td class="text-center" rowspan="'.(count($v)+1).'">';
                            $html_tabel .= '<strong>'.$keyV.'</strong>';
                        $html_tabel .= '</td>';
                    $html_tabel .= '</tr>';

                    $no = 1;
                    $alphabet = ['a','b','c','d'];
                    foreach ($v as $key => $q) {
                        $html_tabel .= '<tr>';
                            $html_tabel .= '<td class="text-center">'.$no++.'</td>';
                            $html_tabel .= '<td>';
                                $html_tabel .= '<div class="row">';
                                    $html_tabel .= '<div class="col-md-12">';
                                        $html_tabel .= '<h4><b>Soal :</b></h4>';
                                        $html_tabel .= $q['isi_soal'];
                                    $html_tabel .= '</div>';

                                    $html_tabel .= '<div class="col-md-12">';
                                        $html_tabel .= '<h4><b>Pilihan :</b></h4>';
                                        
                                        foreach ($alphabet as $a) {
                                            if (!$q['pilihan_'.$a]) {
                                                continue;
                                            }
                                            $html_tabel .= '<div class="row">';
                                                $html_tabel .= '<div class="col-md-1 text-center">';
                                                    $html_tabel .= '<strong>'.ucfirst($a.'.').'</strong>';
                                                $html_tabel .= '</div>';
                                                $html_tabel .= '<div class="col-md-11 pl-0">';
                                                    $html_tabel .= $q['pilihan_'.$a];
                                                $html_tabel .= '</div>';
                                            $html_tabel .= '</div>';
                                        }

                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h4><b>Jawaban : '.$q['jawaban'].'</b></h4>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                        $html_tabel .= '<div class="row mb-3">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h4><b>Explanation :</b></h4>';
                                                $html_tabel .= $q['penjelasan_jwb'] ?? '-';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Sesi : </b>'.$q['sesi'].'</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';
                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Topic : </b>'.$q['topic'].' | '.$q['sub_topic'].'</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';
                                        $html_tabel .= '<div class="row">';
                                            $html_tabel .= '<div class="col-md-12">';
                                                $html_tabel .= '<h5><b>Durasi : </b>'.$q['durasi'].' menit</h5>';
                                            $html_tabel .= '</div>';
                                        $html_tabel .= '</div>';

                                    $html_tabel .= '</div>';
                                    
                                $html_tabel .= '</div>';
                            $html_tabel .= '</td>';

                            if (!$or || ($or['status'] == 0 || $or['status'] == 3)) {
                                $html_tabel .= '<td width="10%" class="text-center">';
                                    $html_tabel .= '<span onclick="ExerciseEditQ('.$q['id_lat'].')" class="btn btn-success mr-2 mb-2"><i class="fa fa-edit"></i></span>';
                                    $html_tabel .= '<span class="btn btn-danger mb-2" 
                                                    onclick="ExerciseDelQ('.$q['id_lat'].')"><i class="fa fa-trash"></i></span>';
                                $html_tabel .= '</td>';
                            }

                        $html_tabel .= '</tr>';
                    }
                }
        }
        if ($latihan->count() > 0) {

                for ($i=1; $i <= ($latihan->max('varian_latihan') + 1); $i++) {
                    if (($latihan->max('varian_latihan') + 1) == $i) {
                        $html_variant .= '<option selected>'.$i.'</option>';
                     } else{
                        $html_variant .= '<option>'.$i.'</option>';
                     }
                }

        }else{
            $html_variant .= '<option selected>1</option>';
        }
        
        $html_variant .= '</select>';

        $ret = [
            'html_tabel_exercise' => $html_tabel,
            'exercise_data' => $latihanData,
            'html_varian_exercise' => $html_variant
        ];
        
        return response()->json($ret);
    }

    public function questionStoreExercise(Request $request, $id)
    {
        $request['id_pm'] = $id;
        $save = Latihan::insert($request->all());
        return response()->json($save);
    }

    public function deleteQuestionExercise(Request $request, $id)
    {
        $delete = Latihan::where('id_lat',$request->id)->delete();

        return response()->json($delete);
    }

    public function summary($id)
    {
        $html = '';

        // $orFile = [];
        // $orFileData = OrFileModel::leftJoin('topic','topic.id_topic','=','or_file.topic_id')
        //                             ->selectRaw('or_file.*,CONCAT(topic," - ",sub_topic) as topic')
        //                             ->where('or_file.id_pm',$id)->get();
        // if ($orFileData->count() > 0) {

        //     foreach ($orFileData as $key => $v) {
        //         $orFile[$v->type] = $orFileData->where('type',$v->type);
        //     }
        // }

        // if (isset($orFile['or_ppt'])) {
            
        //     $html .= '<div class="row">';
        //         $html .= '<h3>PPT</h3>';
        //         $html .= '<hr>';
        //         $html .= '<div class="col-md-8">';
        //             $html .= '<table class="table">';
        //                 $html .= '<tr>';
        //                     $html .= '<th>Topic</th>';
        //                     $html .= '<th>File</th>';
        //                 $html .= '</tr>';

        //                 $no = 1;
        //                 foreach ($orFile['or_ppt'] as $key => $v) {
        //                     $html .= '<tr>';
        //                         $html .= '<td>'.$v['topic'].'</td>';
        //                         $html .= '<td>';
        //                             $html .= '<a href="'.Storage::url(contents_path().'or_ppt/'.$v->file).'">';
        //                                 $html .= '<i class="fa fa-file"></i> View';
        //                             $html .= '</a>';
        //                         $html .= '</td>';
        //                     $html .= '</tr>';
        //                 }

        //             $html .= '</table>';
        //         $html .= '</div>';
        //     $html .= '</div>';
        // }

        // if (isset($orFile['or_ln'])) {
            
        //     $html .= '<div class="row">';
        //         $html .= '<h3>LN</h3>';
        //         $html .= '<hr>';
        //         $html .= '<div class="col-md-8">';
        //             $html .= '<table class="table">';
        //                 $html .= '<tr>';
        //                     $html .= '<th>Topic</th>';
        //                     $html .= '<th>File</th>';
        //                 $html .= '</tr>';
        //                 $no = 1;
        //                 foreach ($orFile['or_ln'] as $key => $v) {
        //                     $html .= '<tr>';
        //                         $html .= '<td>'.$v['topic'].'</td>';
        //                         $html .= '<td>';
        //                             $html .= '<a href="'.Storage::url(contents_path().'or_ln/'.$v->file).'">';
        //                                 $html .= '<i class="fa fa-file"></i> View';
        //                             $html .= '</a>';
        //                         $html .= '</td>';
        //                     $html .= '</tr>';
        //                 }

        //             $html .= '</table>';
        //         $html .= '</div>';
        //     $html .= '</div>';
        // }

        // if (isset($orFile['or_video'])) {
            
        //     $html .= '<div class="row">';
        //         $html .= '<h3>Video</h3>';
        //         $html .= '<hr>';
        //         $html .= '<div class="col-md-8">';
        //             $html .= '<table class="table">';
        //                 $html .= '<tr>';
        //                     $html .= '<th>Topic</th>';
        //                     $html .= '<th>File</th>';
        //                 $html .= '</tr>';
        //                 $no = 1;
        //                 foreach ($orFile['or_video'] as $key => $v) {
        //                     $html .= '<tr>';
        //                         $html .= '<td>'.$v['topic'].'</td>';
        //                         $html .= '<td>';
        //                             $html .= '<a href="'.Storage::url(contents_path().'or_video/'.$v->file).'">';
        //                                 $html .= '<i class="fa fa-file"></i> View';
        //                             $html .= '</a>';
        //                         $html .= '</td>';
        //                     $html .= '</tr>';
        //                 }

        //             $html .= '</table>';
        //         $html .= '</div>';
        //     $html .= '</div>';
        // }

        // if (isset($orFile['or_materi_pendukung'])) {
            
        //     $html .= '<div class="row">';
        //         $html .= '<h3>Materi Pendukung</h3>';
        //         $html .= '<hr>';
        //         $html .= '<div class="col-md-8">';
        //             $html .= '<table class="table">';
        //                 $html .= '<tr>';
        //                     $html .= '<th>Topic</th>';
        //                     $html .= '<th>Judul</th>';
        //                     $html .= '<th>Link</th>';
        //                     $html .= '<th>File</th>';
        //                 $html .= '</tr>';
        //                 $no = 1;
        //                 foreach ($orFile['or_materi_pendukung'] as $key => $v) {
        //                     $html .= '<tr>';
        //                         $html .= '<td>'.$v['topic'].'</td>';
        //                         $html .= '<td>'.$v['title'].'</td>';
        //                         $html .= '<td><a target="_blank" href="'.$v['link'].'">'.$v['link'].'</a></td>';
        //                         $html .= '<td>';
        //                             $html .= '<a href="'.Storage::url(contents_path().'or_materi_pendukung/'.$v->file).'">';
        //                                 $html .= '<i class="fa fa-file"></i> View';
        //                             $html .= '</a>';
        //                         $html .= '</td>';
        //                     $html .= '</tr>';
        //                 }

        //             $html .= '</table>';
        //         $html .= '</div>';
        //     $html .= '</div>';
        // }

        $latihan = Latihan::where('id_pm',$id)
                      ->select(
                                'id_lat',
                                'durasi',
                                'isi_soal',
                                'jawaban',
                                'pilihan_a',
                                'pilihan_b',
                                'pilihan_c',
                                'pilihan_d',
                                'penjelasan_jwb',
                                'varian_latihan'
                               )
                      ->orderBy('varian_latihan','ASC')
                      ->orderBy('id_lat','ASC');

        $kuis = Kuis::where('id_pm',$id)
                      ->select(
                                'id_kuis',
                                'durasi',
                                'isi_soal',
                                'jawaban',
                                'pilihan_a',
                                'pilihan_b',
                                'pilihan_c',
                                'pilihan_d',
                                'penjelasan_jwb',
                                'varian_latihan'
                               )
                      ->orderBy('varian_latihan','ASC')
                      ->orderBy('id_kuis','ASC');
                      
        if ($kuis->count() > 0) {
            
            $html .= '<div class="row">';
                $html .= '<h3>Total Latihan</h3>';
                $html .= '<hr>';
                $html .= '<div class="col-md-8">';
                    $html .= '<table class="table">';
                        $html .= '<tr>';
                            $html .= '<th>Total Set</th>';
                            $html .= '<th>Total Soal</th>';
                        $html .= '</tr>';
                        $html .= '<tr>';
                            $html .= '<th>'.$latihan->max('varian_latihan').'</th>';
                            $html .= '<th>'.$latihan->count().'</th>';
                        $html .= '</tr>';

                    $html .= '</table>';
                $html .= '</div>';
            $html .= '</div>';
        }

        if ($kuis->count() > 0) {
            
            $html .= '<div class="row">';
                $html .= '<h3>Total Kuis</h3>';
                $html .= '<hr>';
                $html .= '<div class="col-md-8">';
                    $html .= '<table class="table">';
                        $html .= '<tr>';
                            $html .= '<th>Total Set</th>';
                            $html .= '<th>Total Soal</th>';
                        $html .= '</tr>';
                        $html .= '<tr>';
                            $html .= '<th>'.$kuis->max('varian_latihan').'</th>';
                            $html .= '<th>'.$kuis->count().'</th>';
                        $html .= '</tr>';

                    $html .= '</table>';
                $html .= '</div>';
            $html .= '</div>';
        }


        $ret = [
                'tabel_or_detail' => $html
        ];

        return response()->json($ret);
    }
}
