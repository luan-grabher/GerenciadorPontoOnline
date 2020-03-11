<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PagarMe\Client;

class PagarmeRecebimento extends Model
{
    public static function getJsonFromAPI(int $start, int $end){
        $pagarmeImport = new PagarmeImport(
            "balanceOperations",
            [
                "start_date" => "$start",
                "end_date" => "$end"
            ],
            [
                new PagarmeObjectAttribute("dataRecebimento", ['date_created']),
                new PagarmeObjectAttribute("idOperacao", ['id']),
                new PagarmeObjectAttribute("idTransacao", ['movement_object', 'transaction_id']),
                new PagarmeObjectAttribute("status", ['movement_object', 'status']),
                new PagarmeObjectAttribute("parcela", ['movement_object', 'installment'], 1),
                new PagarmeObjectAttribute("dataPagamento", ['movement_object', 'payment_date']),
                new PagarmeObjectAttribute("metodoPagamento", ['movement_object', 'payment_method'],'none'),
                new PagarmeObjectAttribute("entrada", ['amount'],0),
                new PagarmeObjectAttribute("saida", ['fee'],0)
            ]
        );
        $pagarmeImport->import();

        return $pagarmeImport->getResults();
    }

    public static function importDataFromAPIToDatabase(int $start, int $end): array {
        $messages = new Messages();

        try {
            //Buscar dados da api
            $data = self::getJsonFromAPI($start,$end);

            //imprimir dados da api
            foreach ($data as $d){
                try {
                    if(is_integer($d['idTransacao'])) {
                        $recebimento = PagarmeRecebimento::where('idOperacao', $d['idOperacao'])->first();

                        $recebimento = !$recebimento == null ? $recebimento : new PagarmeRecebimento();

                        $recebimento->idOperacao = $d['idOperacao'];
                        $recebimento->idTransacao = $d['idTransacao'];
                        $recebimento->status = $d['status'];
                        $recebimento->metodoPagamento = $d['metodoPagamento'];
                        $recebimento->parcela = $d['parcela'];
                        $recebimento->idTransacao = $d['idTransacao'];

                        $dataRecebimento = new \DateTime($d['dataRecebimento']);
                        $dataPagamento = new \DateTime($d['dataPagamento']);
                        $recebimento->dataRecebimento = $dataRecebimento;
                        $recebimento->dataPagamento = $dataPagamento;


                        $recebimento->entrada = $d['entrada'];
                        $recebimento->saida = $d['saida'];
                        $recebimento->save();
                    }
                }catch (\Exception $e){
                    $messages->add('Ocorreu um erro desconhecido com um recebimento: ' . $e->getMessage(),'danger');
                }
            }

            $messages->add("Importação concluída! Operações importadas/atualizadas: " . sizeof($data),'success');

        }catch (\Exception $e){
            //Exibir erro
            $messages->add('Ocorreu um erro desconhecido: ' . $e->getMessage(),'danger');
        }

        return $messages->getArray();
    }
}
