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
        select(['idTransacao as tid','status', 'metodoPagamento as pagamento','parcela'])->
        selectRaw("
            date_format(dataRecebimento, '%d/%m/%Y') as recebimento,
            date_format(dataPagamento, '%d/%m/%Y') as pagamento,
            concat('R$',FORMAT(entrada/100,2,'de_DE')) as 'entrada',
            concat('R$',FORMAT(saida/100,2,'de_DE')) as 'saida'
        ")->
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
        $search = PagarmeVenda::
        select(['tid','cliente','status', 'metodoPagamento as pagamento'])->
        selectRaw("
            date_format(dataPagamento, '%d/%m/%Y') as 'data pagamento',
            parcelas,
            concat('R$',FORMAT(valor/100,2,'de_DE')) as 'valor',
            concat('R$',FORMAT(valorAutorizado/100,2,'de_DE')) as 'valor autorizado',
            concat('R$',FORMAT(valorPago/100,2,'de_DE')) as 'valor pago'
        ")->
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
