<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\JsonRequest;
use Illuminate\Http\Request;

class ErpController extends Controller
{
    public function jsonVendas(JsonRequest $request){
        $datas = $request->getStartEnd();
        return ErpVenda::getJsonFromErp($datas['start'],$datas['end']);
    }
}
