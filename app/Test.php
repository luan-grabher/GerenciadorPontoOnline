<?php

namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        /*$start = 1577836800000;
        $end = 1578023200000;
        $pagarmeImport = new PagarmeImport(
            "transactions",
            [
                "date_created" => [">=$start","<$end"]
            ],
            [
                new PagarmeObjectAttribute("tid", ['tid']),
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

        return "<pre>".json_encode($pagarmeImport->getResults(),JSON_PRETTY_PRINT)."</pre><br>";*/
    }

}
