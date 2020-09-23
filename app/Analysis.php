<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PagarmeRecebimento;

class Analysis extends Model
{
    public static function getTidBalance(string $start, string $end){
        $results = [];

        /**
         * Pega vendas do pagarme com tid, cliente e metodo de pagamento
         * Pega a data da venda, o número de parcelas e o valor da venda
         */
        $sales =
            PagarmeVenda::
            select(['tid','cliente','metodoPagamento as pagamento'])
                ->selectRaw("date_format(dataPagamento,'%d/%m/%Y') as 'data venda', parcelas, valorPago as 'valor venda'")
                ->where([
                    ['status','=','paid'],
                    ['valorPago','>','0']
                ])->get()->toArray();

        //For each sale get totals
        foreach ($sales as $sale){
            $recebimentos =  PagarmeRecebimento::
            selectRaw("
                 sum(if(status = 'paid' and entrada > 0,1,0)) as 'parcelas pagas'
                , sum(if(entrada >0,entrada,0)) as 'total recebido'
                , sum(if(entrada <0,entrada,0)) as 'total devolvido'
                , " . $sale['valor venda'] . " - sum(if(entrada>0,entrada,0)) as 'a receber'
            ")->where('idTransacao','=',$sale['tid'])->groupBy('idTransacao')->first();

            $sale['parcelas pagas'] = isset($recebimentos)?$recebimentos['parcelas pagas']:0;
            $sale['total recebido'] = isset($recebimentos)?$recebimentos['total recebido']:0;
            $sale['total devolvido'] = isset($recebimentos)?$recebimentos['total devolvido']:0;
            $sale['a receber'] = isset($recebimentos)?$recebimentos['a receber']:0;

            $results[] = $sale;
        }

        return $results;
    }
}
