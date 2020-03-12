<?php

namespace App\Http\Controllers;

use App\Http\Requests\RangeDateRequest;
use App\Jobs\ImportPagarmeBalanceoperations;
use App\pagarmeRecebimento;
use App\PagarmeVenda;

class PagarmeController extends Controller
{

    public function importRecebimentosFromAPI(RangeDateRequest $request){
        $dates = $request->getStartEnd();

        return view(
            'layouts.import',
            [
                'title'=>"Importar Pagarme Recebimentos",
                'button_name' => "Importar",
                'messages'=> pagarmeRecebimento::importDataFromAPIToDatabase($dates['start'],$dates['end'])
            ]
        );
    }
    public function importVendasFromAPI(RangeDateRequest $request){
        $dates = $request ->getStartEnd();
        return view(
            'layouts.searchWithRange',
            [
                'title'=>"Importar Vendas Pagarme",
                'button_name'=>'Importar',
                'messages' => PagarmeVenda::importFromAPI($dates['start'],$dates['end'])
            ]
        );
    }

    public function viewGetRecebimentos(RangeDateRequest $request)
    {
        $dates = [
            'start'=>
                date("Y-m-d",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-m-d",strtotime($request->input('fim')))
        ];
        $sales = PagarmeRecebimento::
        whereDate(
            'dataRecebimento', ">=", $dates['start'])->
        whereDate(
            'dataRecebimento', "<", $dates['end']
        )->get();
        return view('layouts.consult',
            ['title'=>'Pagarme Consultar Recebimentos',
                'results' => $sales->toArray(),
                'filters'=>[
                    'Início'=>$dates['start'],
                    'Fim'=>$dates['end'],
                ]
            ]);
    }
    public function viewGetVendas(RangeDateRequest $request){
        $dates = [
            'start'=>
                date("Y-m-d",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-m-d",strtotime($request->input('fim')))
        ];
        $search = PagarmeRecebimento::
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end']
        )->get();
        return view('layouts.consult',
            ['title'=>'Pagarme Consultar Vendas',
                'results' => $search->toArray(),
                'filters'=>[
                    'Início'=>$dates['start'],
                    'Fim'=>$dates['end'],
                ]
            ]);
    }
}
