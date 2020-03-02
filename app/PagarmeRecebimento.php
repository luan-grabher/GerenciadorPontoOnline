<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;
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
        while ($continue & $page<env("PAGARME_MAX_PAGES")){
            try {
                $page++;

                /*$pageResults = $client->balanceOperations()->getList([
                    "count" => 1000,
                    "page" => $page,
                    "end_date" => "=$fim"
                ]);*/



                $resultados[] = ['Pagina'=>$page, 'Num Resultados'=>sizeof($pageResults)];

                if(sizeof($pageResults) > 0){
                    /*foreach ($pageResults as $pageResult){
                        $recebimento = null;
                        try {
                            $recebimento = [
                                "dataRecebimento"=>$pageResult->date_created,
                                "idOperacao"=>$pageResult->id,
                                "idTransacao" =>$pageResult->movement_object->id,
                                "status"=>$pageResult->movement_object->status,
                                "metodoPagamento"=> $pageResult->movement_object->payment_method,
                                "parcela"=>$pageResult->movement_object->installment,
                                "dataPagamento"=>$pageResult->movement_object->payment_date,
                                "entrada"=>$pageResult->amount,
                                "saida"=>$pageResult->fee
                            ];
                            $resultados[] = $recebimento;
                        }catch (\Exception $e){
                            $resultados[] =[
                                "Erro"=>$e->getMessage(),
                                "Objeto"=>$recebimento
                            ];
                        }

                    }*/
                }else{
                    $continue = false;
                }
            }catch (\Exception $e){
                $resultados[] = ["Erro"=>$e->getMessage()];
                $continue = false;
            }
        }



        return "<pre>" . json_encode($resultados, JSON_PRETTY_PRINT) . "</pre>";
    }
}
