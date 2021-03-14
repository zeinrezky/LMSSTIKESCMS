<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;

class RpsTopik extends Model
{
  use SoftDeletes;
  protected $table = 'rps_topik';
  protected $fillable = [
    'id_rps','id_cp','sesi','topik','type'
  ];
  public static function validate($validate)
  {
    $rule = [
      'id_rps' => 'required',
      'id_cp' => 'required',
      'sesi' => 'required',
      'topik' => 'required',
      'type' => 'required',
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
