<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\RangeDateRequest;
use App\Messages;
use App\PagarmeRecebimento;
use App\PagarmeVenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function getTotalTid(RangeDateRequest $request)
    {
        $dates = [
            "start" => $request->input('inicio'),
            "end" => $request->input('fim')
        ];

        $results =
            PagarmeVenda::query()->from('pagarme_vendas as pv')->
            select(['tid','cliente','metodoPagamento as pagamento','parcelas'])
                ->selectRaw("date_format(pv.dataPagamento,'%d/%m/%Y') as 'data venda'")
                ->selectSub("select count(pr.id) from pagarme_recebimentos pr where pr.idTransacao = pv.tid and pr.entrada >0 and pr.status = 'paid') as 'parcelas pagas'")
                ->whereRaw("status = 'paid' and valorPago <> 0")->groupBy([
                    'pv.dataPagamento','pv.tid','pv.cliente','pv.metodoPagamento','pv.parcelas'
                ]);

        $messages = new Messages();
        $messages->add($results->toSql());

        return view(
            "layouts.consult",
            [
                'title' => "Totais TID",
                "messages"=>$messages,
                "results" => $results->get()->toArray()
            ]
        );
    }
}
