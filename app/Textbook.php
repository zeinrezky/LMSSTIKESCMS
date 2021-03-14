<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;

class Textbook extends Model
{
  use SoftDeletes;
  protected $table = 'textbook';
  protected $fillable = [
    'judul','pengarang','isbn','tahun_terbit','edisi','penerbit','kota','kategori','cover','status'
  ];
  public static function validate($validate)
  {
    $rule = [
      'judul' => 'required',
      'pengarang' => 'required',
      'isbn' => 'required',
      'tahun_terbit' => 'required',
      'edisi' => 'required',
      'penerbit' => 'required',
      'kota' => 'required',
      'kategori' => 'required',
      'cover' => 'required',
      'status' => 'required',
    ];
    $validator = Validator::make($validate, $rule);
    if ($validator->fails()) {
      $errors =  $validator->errors()->all();
      $res = array(
        'status' => false,
        'error' => $errors,
        'msg' => 'Error on Validation'
      );
    } else {
      $res = array(
        'status' => true,
        'msg' => 'Validation Ok'
      );
    }
    return $res;
  }
}
