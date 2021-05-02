<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "role_id" => "required",
            "NIP" => "required|max:20",
            "nama" => "required|max:80",
            "email" => "required|email|max:80",
            "telepon" => "required|max:20",
            "password" => "required|max:20",
            "verifikasi_password" => "same:password",
            "alamat" => "max:80",
            "kelamin" => "required",
            "foto" => "image",
        ];
    }
}
