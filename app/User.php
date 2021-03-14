<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;

class User extends Model
{
  use SoftDeletes;
  protected $table = 'user';
  protected $fillable = [
    'nip','name','password','plain_password','email','phone','address','gender','role','status','image'
  ];
  protected $hidden = [
    'password'
  ];
  public static function validate($validate)
  {
    if(!$validate['id']){
      $rule = [
        'nip' => 'required|unique:App\User,nip',
        'name' => 'required',
        'password' => 'required',
        'email' => 'required|unique:App\User,email',
        'phone' => 'required|unique:App\User,phone',
        'role' => 'required',
      ];
    } else {
      $rule = [
        'nip' => 'required|unique:App\User,nip,'.$validate['id'],
        'name' => 'required',
        'email' => 'required|unique:App\User,email,'.$validate['id'],
        'phone' => 'required|unique:App\User,phone,'.$validate['id'],
        'role' => 'required',
      ];
    }
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
