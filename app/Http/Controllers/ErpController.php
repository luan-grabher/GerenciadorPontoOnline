<?php

namespace App\Http\Controllers;

use App\Http\Requests\JsonRequest;
use Illuminate\Http\Request;

class ErpController extends Controller
{
    public function jsonVendas(JsonRequest $request){
        $datas = $request->getStartEnd();
        return "Inicio: " . $datas['start'] . "<br>Fim: " . $datas['end'];
    }
}
