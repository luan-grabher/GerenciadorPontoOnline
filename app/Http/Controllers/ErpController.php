<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\JsonRequest;
use App\Http\Requests\RangeDateRequest;
use Illuminate\Http\Request;

class ErpController extends Controller
{
    public function pageImportVendas()
    {
        return view('layouts.import',['title'=>"Importar ERP Vendas",'button_name' => "Importar"]);
    }

    public function pageImportVendasStartImport(RangeDateRequest $request)
    {
        $dates = $request->getStartEnd();

        return view(
            'layouts.import',
            [
                'title'=>"Importar ERP Vendas",
                'button_name' => "Importar",
                'messages' => ErpVenda::importDataFromERPToDatabase($dates['start'], $dates['end'])
            ]
        );
    }

    public function pageConsultVendas()
    {
        return view('layouts.consult',['title'=>"ERP Consultar Vendas"]);
    }

    public function pageConsultVendasRequest(RangeDateRequest $request)
    {
        $dates = [
            'start'=>
                date("Y-m-d",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-m-d",strtotime($request->input('fim')))
        ];
        $sales = ErpVenda::
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end']
        )->get();
        return view('layouts.consult',
            ['title'=>'ERP Consultar Vendas',
                'results' => $sales->toArray(),
                'filters'=>[
                    'Início'=>$dates['start'],
                    'Fim'=>$dates['end']
                ]
            ]);
    }
}
