<?php

namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test()
    {
        $dateStart = "01/01/2020 00:00:00";
        $dateFinal = "31/01/2020 00:00:00";

        $uri_erp_home = "http://portaladm.pontodosconcursos.com.br";
        $uri_erp_login = $uri_erp_home . "/Auth/Login";
        $uri_er_report = $uri_erp_home . "/Pedido/RelatorioPedidoMensal";

        $selector_nextPage = ".PagedList-skipToNext";

        $user = "admin";
        $pass = "@%159pdc";
        $client = new Client();

        $crawler = $client->request('GET', $uri_erp_login);

        // select the form and fill in some values
        $form = $crawler->selectButton('Entrar')->form();
        $form['UserName'] = $user;
        $form['Password'] = $pass;

        // submit that form
        $crawler = $client->submit($form);

        //Extract data
        $crawler = $client->request('GET', $uri_er_report);
        $form = $crawler->selectButton('Pesquisar')->form();
        $form['dataInicio'] = $dateStart;
        $form['dataFinal'] = $dateFinal;
        $crawler = $client->submit($form);

        $htmlTable = $crawler->filter("#partialPedidoMensal")->html();

        $next = $crawler->filter($selector_nextPage);
        while($next->count() > 0){
            $next->link()->getUri();
        }

        return $crawler->filter("#partialPedidoMensal")->html();
    }
}
