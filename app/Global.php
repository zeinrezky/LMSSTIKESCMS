<?php

// use DateTime;
use App\Models\Agent;
use App\Models\PmAssign;
use App\Services\PushNotif;
use App\Mail\Notification;
use App\Models\TextBook;
use App\Models\Rps;
use App\Models\OrModel;

use Illuminate\Support\Facades\Mail;

function contents_path($append = "")
{
    return "public/contents/" . $append;
}

function agent()
{
    return new Agent();
}

function agent_pending()
{
    $agent = agent()->where("status_id", 1)->count();

    return $agent;
}

function agent_active()
{
    $agent = agent()->whereIn("status_id", [2, 4])->count();

    return $agent;
}

function push_notif()
{
    $push = new PushNotif();

    return $push;
}

function uang($int)
{
    return str_replace(",", ".", number_format($int));
}

function setting_static()
{
    return new \App\Models\SettingStatic();
}

function nameOfMonth($monthNum)
{
    $dateObj   = \DateTime::createFromFormat('!m', $monthNum);
    return $monthName = $dateObj->format('F'); // March
}

function statusCaption($val, $badge = false)
{
    if ($badge) {
        $arr = [
                '<span class="label label-primary">Waiting </span>',
                '<span class="label label-info">Reviewed / Waiting Approval</span>',
                '<span class="label label-success">Approved</span>',
                '<span class="label label-danger">Reject</span>'
               ];
    }else{
        $arr = ['Waiting ','Reviewed / Waiting Approval','Approved','Reject'];
    }

    return $arr[$val];
}

function statusProgressReport($val)
{
    $arr = ['Sudah dibuat dan Menunggu Review','Sudah Direview dan Menunggu Approval','Sudah di-Approve','Rejected'];
    if ($val || $val === 0) {
        return $arr[$val];
    }

    return 'Belum dibuat';
}

function checklistIcon($val)
{
    $arr = ['<i class="fas fa-times text-danger"></i>' , '<i class="fa fa-check text-success"></i>'];
    return $arr[$val];
}

function detailPmA($id)
{
    $data = PmAssign::
    select(
            'id_pm_assign',
            'pengembang_materi.id_pm',
            'sme_id',
            'reviewer_id',
            'approval_id',

            'semester.nama_semester', 
            'matakuliah.mk_nama',

            // SME
            'sme.nama as sme_nama',
            'sme.nip as sme_nip',
            'sme.email as sme_email',

            // Reviewer
            'rev.nama as reviewer_nama',
            'rev.nip as reviewer_nip',
            'rev.email as reviewer_email',

            // Reviewer
            'app.nama as approv_nama',
            'app.nip as approv_nip',
            'app.email as approv_email'
            )
    ->leftJoin('dosen as sme','sme.id_dosen','=','pm_assign.sme_id')
    ->leftJoin('dosen as rev','rev.id_dosen','=','pm_assign.reviewer_id')
    ->leftJoin('dosen as app','app.id_dosen','=','pm_assign.approval_id')
    ->leftJoin('pengembang_materi','pengembang_materi.id_pm','=','pm_assign.id_pm')
    ->leftJoin("semester", "semester.id_semester", "=", "pengembang_materi.id_semester")
    ->leftJoin("matakuliah", "matakuliah.id_matakuliah", "=", "pengembang_materi.id_matakuliah")
    ->where('pengembang_materi.id_pm',$id)
    ->first();

    return $data;
}

function sendEmail($pmId, $type = 'text-book' , $status = 'input', $from)
{
    $d = detailPmA($pmId);

    $textbookData = TextBook::where('id_pm',$pmId)->first();

    if ($type == 'text-book') {
        $typeCaption = 'Text Book';
    }elseif ($type == 'rps') {
        $typeCaption = 'RPS';
    }elseif ($type == 'or') {
        $typeCaption = 'OR';
    }elseif ($type == 'assign') {
        $typeCaption = 'Assign';
    }
    
    $as = 'sme';
    if ($from == $d['reviewer_id']) {
        $as = 'reviewer';
    }elseif ($from == $d['approval_id']) {
        $as = 'approve';
    }
    // msg content

    $msgContent = "<hr>";
    $msgContent = "<br><br><strong>NIP : </strong>".$d['sme_nip']."<br>";
    $msgContent .= "<strong>Nama : </strong>".ucwords($d['sme_nama'])."<br>";

    $msgContent .= "<br>untuk";

    $msgContent .= "<br><br><strong>Semester :</strong> ".$d['nama_semester']."<br>";
    $msgContent .= "<strong>Mata Kuliah :</strong> ".$d['mk_nama']."<br><br>";

    $msgContent .= "Dengan data Text Book sebagai berikut ini:<br>";

    if ($textbookData) {
        $msgContent .= "<br><strong>Judul : </strong>".$textbookData['title'];
        $msgContent .= "<br><strong>Pengarang : </strong>".$textbookData['author'];
        $msgContent .= "<br><strong>Tahun Terbit : </strong>".$textbookData['tahun'];
        $msgContent .= "<br><strong>Kategori : </strong>".$textbookData['kategori'];
    }
    
    $det = false;
    if ($type == 'text-book') {
        $det = $textbookData;
    }elseif ($type == 'rps') {
        $det = Rps::where('id',$pmId)->first();
    }elseif ($type == 'or') {
        $det = OrModel::where('id',$pmId)->first();
    }

    if ($det) {
        if ($det['reviewer_commen']) {
            $msgContent .= "<br><br><hr><br>";
            $msgContent .= "Komentar <strong>Reviewer :</strong><br>";
            $msgContent .= "<small> Oleh : ".ucwords($d['reviewer_nama'])." | ".$d['reviewer_nip']."</small><br>";
            $msgContent .= "<p>".$det['reviewer_commen']."</p>";
        }

        if ($det['approv_commen']) {
            $msgContent .= "<br><br><hr><br>";
            $msgContent .= "Komentar <strong>Approver :</strong><br>";
            $msgContent .= "<small> Oleh : ".ucwords($d['approv_nama'])." | ".$d['approv_nip']."</small><br>";
            $msgContent .= "<p>".$det['approv_commen']."</p>";
        }
    }


    if ($status == 'input') {

        // send to reviewer
        // $msg = 'Terdapat <strong>Pengajuan '.$typeCaption.'</strong> oleh:';
        $msg = 'Saat ini Bapak/Ibu telah ditugaskan sebagai Reviewer. Terdapat data '.$typeCaption.' yang harus direview.';


            $mailData = [
                  'name'=> $d['reviewer_nama'],
                  // 'message' => $msg.$msgContent,
                  'message' => $msg,
                  'btn_caption' => 'Lihat Detail '.$typeCaption,
                  'link' => env('APP_URL').'/review-'.$type.'/detail/'.$d['id_pm']
                ];

            if($d['reviewer_email'] != null){
                Mail::to($d['reviewer_email'])
                      ->send(new Notification($mailData));
            }

        // send to approv

            // $mailData['name'] = $d['approv_nama'];
            
            // if($d['approv_email'] != null){
            //     Mail::to($d['approv_email'])
            //           ->send(new Notification($mailData));
            // }

    }elseif ($status == 'reject') {

        $recejctBy = 'Approver';
        if ($as == 'reviewer') {
            $recejctBy = 'Reviewer';
        }

        $msg = 'Pengajuan untuk '.$typeCaption.' berikut ini <strong>Belum Disetujui Oleh '.$recejctBy.'.</strong>';
        // send to SME

            $mailData = [
                  'name'=> $d['sme_nama'],
                  'message' => $msg.$msgContent,
                  'btn_caption' => 'Lihat Detail '.$typeCaption,
                  'link' => env('APP_URL').'/'.$type.'/detail/'.$d['id_pm']
                ];

            if ($type == 'text-book') {
                $mailData['link'] = env('APP_URL').'/input-'.$type.'/detail/'.$d['id_pm'];
            }

            if($d['sme_email'] != null){
                Mail::to($d['sme_email'])
                      ->send(new Notification($mailData));
            }

            // if ($type == 'text-book') {
            //     $mailData['link'] = env('APP_URL').'/review-'.$type.'/detail/'.$d['id_pm'];
            // }
        // send to ?
        // if ($as == 'reviewer') {

        //     $mailData['name'] = $d['approv_nama'];

        //     if($d['approv_email'] != null){
        //         Mail::to($d['approv_email'])
        //               ->send(new Notification($mailData));
        //     }
            
        // }elseif($as == 'approve'){

        //     $mailData['name'] = $d['reviewer_nama'];

        //     if($d['reviewer_email'] != null){
        //         Mail::to($d['reviewer_email'])
        //               ->send(new Notification($mailData));
        //     }
        // }

    }elseif ($status == 'approve') {

        $msgSME = 'Pengajuan untuk '.$typeCaption.' berikut ini telah <strong>Disetujui oleh Approver.</strong>';

        if ($as == 'reviewer') {
            $msgSME = 'Pengajuan untuk '.$typeCaption.' berikut ini telah <strong>Direview dan Disetujui Oleh Reviewer.</strong>';
            $msg = 'Saat ini Bapak/Ibu telah ditugaskan sebagai Approver. Terdapat data '.$typeCaption.' yang harus disetujui.';
        }
        // send to SME

            $mailData = [
                  'name'=> $d['sme_nama'],
                  'message' => $msgSME.$msgContent,
                  'btn_caption' => 'Lihat Detail '.$typeCaption,
                  'link' => env('APP_URL').'/'.$type.'/detail/'.$d['id_pm']
                ];

            if ($type == 'text-book') {
                $mailData['link'] = env('APP_URL').'/input-'.$type.'/detail/'.$d['id_pm'];
            }

            if($d['sme_email'] != null){
                Mail::to($d['sme_email'])
                      ->send(new Notification($mailData));
            }

        // send to ?
        if ($as == 'reviewer') {

            $mailData = [
                  'name'=> $d['sme_nama'],
                  'message' => $msg.$msgContent,
                  'btn_caption' => 'Lihat Detail '.$typeCaption,
                  'link' => env('APP_URL').'/review-'.$type.'/detail/'.$d['id_pm']
                ];

            if ($type == 'text-book') {
                $mailData['link'] = env('APP_URL').'/review-'.$type.'/detail/'.$d['id_pm'];
            }

            $mailData['name'] = $d['approv_nama'];
            if($d['approv_email'] != null){
                Mail::to($d['approv_email'])
                      ->send(new Notification($mailData));
            }
            
        }
        // elseif($as == 'approve'){

        //     $mailData['name'] = $d['reviewer_nama'];

        //     if($d['reviewer_email'] != null){
        //         Mail::to($d['reviewer_email'])
        //               ->send(new Notification($mailData));
        //     }
        // }

    }elseif ($status == 'revision') {
        $msg = 'Terdapat <strong>Pengajuan Revisi '.$typeCaption.'</strong> oleh:';

        // send to reviewer

            $mailData = [
                  'name'=> $d['reviewer_nama'],
                  'message' => $msg.$msgContent,
                  'btn_caption' => 'Lihat Detail '.$typeCaption,
                  'link' => env('APP_URL').'/review-'.$type.'/detail/'.$d['id_pm']
                ];

            if($d['reviewer_email'] != null){
                Mail::to($d['reviewer_email'])
                      ->send(new Notification($mailData));
            }

        // send to approv

            // $mailData['name'] = $d['approv_nama'];

            // if($d['approv_email'] != null){
            //     Mail::to($d['approv_email'])
            //           ->send(new Notification($mailData));
            // }
    
    }elseif ($status == 'assign') {

        $msg = 'Saat ini Bapak/Ibu telah ditugaskan sebagai Dosen Pengembang Materi. Terdapat data text book yang harus diisi.';
        
        // send to SME

            $mailData = [
                  'name'=> $d['sme_nama'],
                  'message' => $msg,
                  'btn_caption' => 'Input Text book',
                  'link' => env('APP_URL').'/input-text-book'
                ];

        if($d['sme_email'] != null){
            Mail::to($d['sme_email'])
                  ->send(new Notification($mailData));
        }
    }


    return true;

    // for see view
    return view('mail.notification')->with('data',$mailData);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getMonthData($val = false)
{

    $arr = [];
    for ($i=1; $i < 13; $i++) { 
        $arr[$i] = carbon()
            ->parse('0000-'.$i.'-01')
            ->format("M");
    }
    
    if ($val) {
        return $arr[$val];
    }

    return $arr;
}

function MPCategories($val = false)
{
    $arr = [
        'TEORI' => 'Teori',
        'PRAKTEK' => 'Praktikum',
        'KLINIK' => 'Klinik'
    ];

    if ($val) {
        return $arr[$val];
    }

    return $arr;
}

function alertMaxSize(){
    return '
        <div class="row">
            <div class="col-md-12">
                <div class="alert" role="alert">
                  <hr>
                  <strong>Batas upload file 20 MB</strong><br>
                  <small>  
                    <span class="text-danger">*</span>jika ada file atau jumlah size file yang lebih dari 20 MB silahkan save berkala dengan klik tombol <strong class="text-success">Save to Draft</strong> (di tab Summary) lalu upload kembali file yang lainnya. 
                  </small> 
                </div>
            </div>
        </div>';
}