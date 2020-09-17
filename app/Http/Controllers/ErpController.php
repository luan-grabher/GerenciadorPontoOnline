<?php

namespace App\Http\Controllers;

use App\Http\Requests\RangeDateRequest;
use App\ImportSalesFromEPR;
use App\Messages;
use App\Sale;

class ErpController extends Controller
{
    public function importVendasFromERP(RangeDateRequest $request)
    {
        $messages = new Messages();

        $dates = $request->getStartEnd();

        $start = new \DateTime(date("Y-m-d",$dates['start']));
        $end = new \DateTime(date("Y-m-d",$dates['end']));

        $importation = new ImportSalesFromEPR($start, $end);
        $resultImportation = $importation->import();
        if(isset($resultImportation['error'])){
            $messages->add($resultImportation['error'],"danger");
        }elseif(isset($resultImportation['sales']) && is_array($resultImportation['sales'])){
            $messages->add("Foram importadas " . sizeof($resultImportation['sales']). " vendas do sistema.", "success");
        }else{
            $messages->add("O programa não retornou nenhuma venda importada e nenhum erro!");
        }

        return view(
            'layouts.import',
            [
                'title'=>"Importar ERP Vendas",
                'button_name' => "Importar",
                'messages' => $messages->getArray()
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
        $sales = Sale::
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
