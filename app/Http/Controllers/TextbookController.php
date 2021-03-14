<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Textbook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TextbookController extends Controller
{
  public function list(Request $request){
    $offset = 0;
    $page = 1;
    $perPage = 10;
    $sortDir = 'ASC';
    $sortBy = 'name';
    $search = null;
    $total = 0;
    $totalPage = 1;
    $role = null;
    $induk = null;
    if ($request->page) {
      $page = $request->page;
    }
    if ($request->perPage) {
      $perPage = $request->perPage;
    }
    if ($request->sortDir) {
      $sortDir = $request->sortDir;
    }
    if ($request->sortBy) {
      $sortBy = $request->sortBy;
    }
    if ($request->search) {
      $search = $request->search;
    }
    if($request->role){
      $role = $request->role;
    }
    if ($page > 1) {
      $offset = ($page - 1) * $perPage;
    }
    $listData = Textbook::orderBy($sortBy, $sortDir);
    if($role){
      $listData->where('role',$role);
    }
    if ($perPage != '~') {
      $listData->skip($offset)->take($perPage);
    }
    if ($search != null) {
      $listData->whereRaw('(textbook.judul LIKE "%'.$search.'%" OR textbook.pengarang LIKE "%'.$search.'%" OR textbook.edisi LIKE "%'.$search.'%" OR textbook.penerbit LIKE "%'.$search.'%")');
    }

    $listData = $listData->get();
    if ($search || $role || $induk) {
      $total = Textbook::orderBy($sortBy, $sortDir);
      if($search)
        $total->whereRaw('(textbook.judul LIKE "%'.$search.'%" OR textbook.pengarang LIKE "%'.$search.'%" OR textbook.edisi LIKE "%'.$search.'%" OR textbook.penerbit LIKE "%'.$search.'%")');
      if($role)
        $total->where('role',$role);
      $total = $total->count();
    } else {
      $total = Textbook::all()->count();
    }

    if ($perPage != '~') {
      $totalPage = ceil($total / $perPage);
    }
    $res = array(
      'status' => true,
      'data' => $listData,
      'page' => $page,
      'perPage' => $perPage,
      'sortDir' => $sortDir,
      'sortBy' => $sortBy,
      'search' => $search,
      'total' => $total,
      'totalPage' => $totalPage,
      'msg' => 'List data available'
    );
    return response()->json($res, 200);
  }
  public function get(Request $request){
    if ($request->id) {
      $getData = Textbook::find($request->id);
      if($getData){
        if($getData->cover){
          $thecover = Storage::disk('public')->url('textbook/'.$getData->cover);
          if($thecover)
            $getData->cover = $thecover;
          else
            $getData->cover = null;
        }
        $res = array(
          'status' => true,
          'data' => $getData,
          'msg' => 'Data available'
        );
      } else {
        $res = array(
          'status' => false,
          'msg' => 'Data not found'
        );
      }
    } else {
      $res = array(
        'status' => false,
        'msg' => 'No data selected'
      );
    }
    return response()->json($res, 200);
  }
  public function create(Request $request){
    $dataCreate = $request->all();
    DB::beginTransaction();
    $file = $request->cover;
    if($file){
      $name = time() . '-textbook.png';
      $filePath = 'textbook/' . $name;
      $dataCreate['cover'] = $name;
    }
    $dataCreate['password'] = Hash::make($request->password);
    $dataCreate['plain_password'] = $request->password;
    $validate = Textbook::validate($dataCreate);
    if ($validate['status']) {
      try {
        if($file)
          Storage::disk('public')->put($filePath, file_get_contents($file));
        $dc = Textbook::create($dataCreate);
        $res = array(
          'status' => true,
          'data' => $dataCreate,
          'msg' => 'Data berhasil disimpan'
        );
        DB::commit();
      } catch (Exception $e) {
        DB::rollback();
        $res = array(
          'status' => false,
          'data' => $dataCreate,
          'msg' => 'Data gagal disimpan'
        );
      }
    } else {
      $res = array(
        'status' => false,
        'data' => $dataCreate,
        'msg' => 'Validasi gagal',
        'errors' => $validate['error']
      );
    }
    return response()->json($res, 200);
  }
  public function update(Request $request){
    $main_cover = $request->cover;
    $updateData = Textbook::find($request->id);
    $updateData->judul = $request->judul;
    $updateData->pengarang = $request->pengarang;
    $updateData->isbn = $request->isbn;
    $updateData->tahun_terbit = $request->tahun_terbit;
    $updateData->edisi = $request->edisi;
    $updateData->penerbit = $request->penerbit;
    $updateData->kota = $request->kota;
    $updateData->kategori = $request->kategori;
    $updateData->status = $request->status;
    if(basename($main_cover) != $updateData->cover){
      $old_cover = $updateData->cover;
      $name = time() . '-textbook.png';
		  $filePath = 'textbook/' . $name;
      Storage::disk('public')->put($filePath, file_get_contents($main_cover));
      Storage::disk('public')->delete('textbook/' . $old_cover);
      $updateData->cover = $name;
    }
    $validate = Textbook::validate($request->all());
    if ($validate['status']) {
      try {
        $updateData->save();
        $res = array(
          'status' => true,
          'msg' => 'Data Successfully Saved'
        );
      } catch (Exception $e) {
        $res = array(
          'status' => false,
          'msg' => 'Failed to Save Data'
        );
      }
    } else {
      $res = $validate;
    }
    return response()->json($res, 200);
  }
  public function delete(Request $request){
    $id = $request->id;
    if ($id) {
      $delData = Textbook::find($id);
      try {
        $delData->delete();
        $res = array(
          'status' => true,
          'msg' => 'Data successfully deleted'
        );
      } catch (Exception $e) {
        $res = array(
          'status' => false,
          'msg' => 'Failed to delete Data'
        );
      }
    } else {
      $res = array(
        'status' => false,
        'msg' => 'No data selected'
      );
    }
    return response()->json($res, 200);
  }
  public function dummy(){
    $data = [
      [
        'nip' => uniqid(),
        'name' => 'ADMIN',
        'password' => Hash::make(123456),
        'plain_password' => 123456,
        'email' => 'admin@stikes.com',
        'phone' => '0812345678901',
        'address' => '-',
        'gender' => 'Pria',
        'role' => 'ADMIN',
        'status' => 'ACTIVE'
      ],
      [
        'nip' => uniqid(),
        'name' => 'REVIEWER',
        'password' => Hash::make(123456),
        'plain_password' => 123456,
        'email' => 'reviewer@stikes.com',
        'phone' => '0812345678901',
        'address' => '-',
        'gender' => 'Pria',
        'role' => 'REVIEWER',
        'status' => 'ACTIVE'
      ],
      [
        'nip' => uniqid(),
        'name' => 'SME',
        'password' => Hash::make(123456),
        'plain_password' => 123456,
        'email' => 'sme@stikes.com',
        'phone' => '0812345678901',
        'address' => '-',
        'gender' => 'Pria',
        'role' => 'SME',
        'status' => 'ACTIVE'
      ],
      [
        'nip' => uniqid(),
        'name' => 'APPROVER',
        'password' => Hash::make(123456),
        'plain_password' => 123456,
        'email' => 'approver@stikes.com',
        'phone' => '0812345678901',
        'address' => '-',
        'gender' => 'Pria',
        'role' => 'APPROVER',
        'status' => 'ACTIVE'
      ],
      [
        'nip' => uniqid(),
        'name' => 'KAJUR',
        'password' => Hash::make(123456),
        'plain_password' => 123456,
        'email' => 'kajur@stikes.com',
        'phone' => '0812345678901',
        'address' => '-',
        'gender' => 'Pria',
        'role' => 'KAJUR',
        'status' => 'ACTIVE'
      ]
    ];
    foreach($data as $dt){
      Textbook::create($dt);
    }
  }
}