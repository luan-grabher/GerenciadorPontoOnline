<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PagarMe\Client;

class PagarmeRecebimento extends Model
{
    public static function getJsonFromAPI(int $inicio,int $fim){
        ini_set ('max_execution_time',  -1);

        //Inicializa cliente
        $client = new Client(env('PAGARME_API_KEY'));

        //Inicializa array de resultados
        $resultados = [];
        $resultados[] = ['start'=>date("d-m-Y",$inicio),"end"=>date("d-m-Y",$fim)];
        $resultados[] = ['start_unix'=>$inicio,"end_unix"=>$fim];

        //Inicializa Pagina
        $page = 0;
        $continue = true;

        //Enquanto não ocorrer erros, continue buscando
        while ($continue){
            try {
                $page++;

                $resultados[] = ['Pagina'=>$page];

                $pageResults = $client->balanceOperations()->getList([
                    "count" => 100,
                    "page" => $page,
                    "start_date" => "=$inicio",
                    "end_date" => "=$fim"
                ]);

                $resultados[] = ['Num Resultados'=>sizeof($pageResults)];

                if(sizeof($pageResults) > 0){
                    foreach ($pageResults as $pageResult){
                        $resultados[] = [
                            "dataRecebimento"=>$pageResult->date_created,
                            "idOperacao"=>$pageResult->movement_object->id,
                            "idTransacao" =>$pageResult->movement_object->transaction_id,
                            "status"=>$pageResult->movement_object->status,
                            "metodoPagamento"=> $pageResult->movement_object->payment_method,
                            "parcela"=>$pageResult->movement_object->installment,
                            "dataPagamento"=>$pageResult->movement_object->date_created,
                            "entrada"=>$pageResult->movement_object->amount,
                            "saida"=>$pageResult->movement_object->fee
                        ];
                    }
                }else{
                    $continue = false;
                }

                //$continue = false;
            }catch (\Exception $e){
                $resultados[] = ["Erro"=>$e->getMessage()];
                $continue = false;
            }
        }



        return "<pre>" . json_encode($resultados, JSON_PRETTY_PRINT) . "</pre>";
    }
}
