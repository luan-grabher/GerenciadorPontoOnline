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
            /***/
        ];
    }

    public function getStartEnd(){
        $oneDay = 86400;

        $start = strtotime("now");
        $end = strtotime("now");

        try {
            $start = strtotime($this->inicio)*1000;
        }catch(\Exception $e){
        }

        try {
            $end = (strtotime($this->fim)+$oneDay)*1000;
        }catch(\Exception $e){
        }

        $end = $end<$start?$start:$end;

        return ["start"=>$start,"end"=>$end];
    }
}
