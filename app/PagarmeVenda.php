<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PagarMe\Client;

class PagarmeVenda extends Model
{
    public static function getFromAPI(int $start, int $end): array
    {
        $pagarmeImport = new PagarmeImport(
            "transactions",
            [
                "date_created" => [">=$start","<$end"]
            ],
            [
                new PagarmeObjectAttribute("tid", ['tid'],0),
                new PagarmeObjectAttribute("cliente", ['customer','name'],0),
                new PagarmeObjectAttribute("status", ['status']),
                new PagarmeObjectAttribute("dataPagamento", ['date_created']),
                new PagarmeObjectAttribute("metodoPagamento", ['payment_method']),
                new PagarmeObjectAttribute("parcelas", ['installments']),
                new PagarmeObjectAttribute("valor", ['amount'],0),
                new PagarmeObjectAttribute("valorAutorizado", ['authorized_amount'],0),
                new PagarmeObjectAttribute("valorPago", ['paid_amount'],0)
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
            $data = self::getFromAPI($start,$end);

            //imprimir dados da api
            foreach ($data as $d){
                $db = PagarmeVenda::where('tid',$d['tid'])->first();

                $db = !$db == null?$db:new PagarmeVenda();

                $db->tid = $d['tid'];
                $db->cliente = $d['cliente'];
                $db->status = $d['status'];
                $db->metodoPagamento = $d['metodoPagamento'];
                $db->parcelas = $d['parcelas'];

                $dataPagamento = new \DateTime($d['dataPagamento']);
                $db->dataPagamento = $dataPagamento;


                $db->valor = $d['valor'];
                $db->valorAutorizado = $d['valorAutorizado'];
                $db->valorPago = $d['valorPago'];
                $db->save();
            }

            $messages->add("Importação concluída! Operações importadas/atualizadas: " . sizeof($data),'success');

        }catch (\Exception $e){
            //Exibir erro
            $messages->add('Ocorreu um erro desconhecido: ' . $e->getMessage(),'danger');
        }

        return $messages->getArray();

    }
}
