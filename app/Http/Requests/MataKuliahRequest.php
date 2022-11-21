<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MataKuliahRequest extends FormRequest
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
        $plus = !empty(request()->segment(3)) ?  ",mk_kode," . request()->segment(3) . ",id_matakuliah" : "";

        return [
            "mk_kode" => "required|max:20|unique:matakuliah" . $plus,
            "mk_nama" => "required|max:80"
        ];
    }
}
