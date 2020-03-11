<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PagarMe\Client;

class PagarmeVenda extends Model
{
    public static function getFromAPI(int $start, int $end): array
    {
        $pagarmeImport = new PagarmeImport(
            "balanceOperations",
            [
                "start_date" => "$start",
                "end_date" => "$end"
            ],
            [
                new PagarmeObjectAttribute("dataRecebimento", ['date_created']),
                new PagarmeObjectAttribute("idOperacao", ['id']),
                new PagarmeObjectAttribute("idTransacao", ['movement_object', 'id']),
                new PagarmeObjectAttribute("status", ['movement_object', 'status']),
                new PagarmeObjectAttribute("parcela", ['movement_object', 'installment'], 1),
                new PagarmeObjectAttribute("dataPagamento", ['movement_object', 'payment_date']),
                new PagarmeObjectAttribute("entrada", ['amount'],0),
                new PagarmeObjectAttribute("saida", ['fee'],0)
            ]
        );
        $pagarmeImport->import();

        return $pagarmeImport->getResults();
    }

    public static function importFromAPI(int $start, int $end): array
    {
        $messages = new Messages();

        try {
            //Buscar dados da api
            $data = self::getJsonFromAPI($start,$end);

            //imprimir dados da api
            foreach ($data as $recebimentoAPI){
                $recebimento = PagarmeRecebimento::where('idOperacao',$recebimentoAPI['idOperacao'])->first();

                $recebimento = !$recebimento == null?$recebimento:new PagarmeRecebimento();

                $recebimento->idOperacao = $recebimentoAPI['idOperacao'];
                $recebimento->idTransacao = $recebimentoAPI['idTransacao'];
                $recebimento->status = $recebimentoAPI['status'];
                $recebimento->metodoPagamento = $recebimentoAPI['metodoPagamento'];
                $recebimento->parcela = $recebimentoAPI['parcela'];
                $recebimento->idTransacao = $recebimentoAPI['idTransacao'];

                $dataRecebimento = new \DateTime($recebimentoAPI['dataRecebimento']);
                $dataPagamento = new \DateTime($recebimentoAPI['dataPagamento']);
                $recebimento->dataRecebimento = $dataRecebimento;
                $recebimento->dataPagamento = $dataPagamento;


                $recebimento->entrada = $recebimentoAPI['entrada'];
                $recebimento->saida = $recebimentoAPI['saida'];
                $recebimento->save();
            }

            $messages->add("Importação concluída! Operações importadas/atualizadas: " . sizeof($data),'success');

        }catch (\Exception $e){
            //Exibir erro
            $messages->add('Ocorreu um erro desconhecido: ' . $e->getMessage(),'danger');
        }

        return $messages->getArray();

    }
}
