<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;

class RpsCp extends Model
{
  use SoftDeletes;
  protected $table = 'rps_cp';
  protected $fillable = [
    'id_rps','cp_name'
  ];
  public static function validate($validate)
  {
    $rule = [
      'id_rps' => 'required',
      'cp_name' => 'required',
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
