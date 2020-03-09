<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\JsonRequest;
use App\Http\Requests\RangeDateRequest;
use Illuminate\Http\Request;

class ErpController extends Controller
{
    public function jsonVendas(JsonRequest $request){
        $datas = $request->getStartEnd();
        return ErpVenda::getJsonFromErp($datas['start'],$datas['end']);
    }

    public function pageImportVendas(){
        return view('import.erp.vendas');
    }

    public function pageImportVendasStartImport(RangeDateRequest $request){
        $dates = $request->getStartEnd();


        return view(
            'import.erp.vendas',
            [
                'messages'=> ErpVenda::importDataFromAPIToDatabase($dates['start'],$dates['end'])
            ]
        );
    }
}
