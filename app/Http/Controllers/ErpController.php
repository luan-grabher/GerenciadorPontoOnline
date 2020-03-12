<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\RangeDateRequest;
use Illuminate\Support\Facades\DB;

class ErpController extends Controller
{
    public function importVendasFromERP(RangeDateRequest $request)
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
    public function viewGetVendas(RangeDateRequest $request)
    {
        $dates = [
            'start'=>
                date("Y-m-d",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-m-d",strtotime($request->input('fim')))
        ];
        $sales = ErpVenda::
        select(['matricula','tid','curso_id','curso','aluno'])->
        selectRaw("
            date_format(dataRecebimento, '%d/%m/%Y') as recebimento,
            date_format(dataPagamento, '%d/%m/%Y') as pagamento,
            metodoPagamento,
            concat('R$',FORMAT(valorTotal/100,2,'de_DE')) as 'valor tid',
            concat('R$',FORMAT(valorCursoSD/100,2,'de_DE')) as 'valor curso',
            concat('R$',FORMAT(valorCursoCD/100,2,'de_DE')) as 'valor pago',
            concat('R$',FORMAT(pgamenos/100,2,'de_DE')) as 'desconto'/*,
            concat('R$',FORMAT(credUtilizado/100,2,'de_DE')) as 'credito utilizado',
            concat('R$',FORMAT(credAluno/100,2,'de_DE')) as 'credito aluno'*/
        ")->
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end'])->
        get();

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
