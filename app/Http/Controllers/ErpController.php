<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\JsonRequest;
use App\Http\Requests\RangeDateRequest;
use Illuminate\Http\Request;

class ErpController extends Controller
{
    public function jsonVendas(JsonRequest $request)
    {
        $datas = $request->getStartEnd();
        return ErpVenda::getJsonFromErp($datas['start'], $datas['end']);
    }

    public function pageImportVendas()
    {
        return view('import.erp.vendas');
    }

    public function pageImportVendasStartImport(RangeDateRequest $request)
    {
        $dates = $request->getStartEnd();

        return view(
            'import.erp.vendas',
            [
                'messages' => ErpVenda::importDataFromERPToDatabase($dates['start'], $dates['end'])
            ]
        );
    }

    public function pageConsultVendas()
    {
        return view('consult.erp.vendas');
    }

    public function pageConsultVendasRequest(RangeDateRequest $request)
    {
        $dates = [
            'start'=>
                date("Y-d-m",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-d-m",strtotime($request->input('fim')))
        ];
        $sales = ErpVenda::
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end']
        )->get();
        return view('consult.erp.vendas', ['results' => $sales->toArray(),'filters'=>['dates'=>$dates]]);
    }
}
