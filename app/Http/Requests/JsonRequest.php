<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JsonRequest extends FormRequest
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
            "inicio" => "required",//|date_format:y-m-d",
            "fim" => "required",//|date_format:y-m-d|after:inicio",
            "page" => "required"//|integer"
        ];
    }
}
