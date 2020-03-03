<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;

class ErpVenda extends Model
{
    private function config()
    {
        return [
            "css_nextPage" => ".PagedList-skipToNext",
            "nameButtonLogin" => "Entrar",
            "nameButtonSearch" => "Pesquisar",
            "css_tableResult" => "#partialPedidoMensal"
        ];
    }

    public function getJsonFromErp(int $start, int $end)
    {
        //Prepare function
        $date_start = date("UTC",$start);
        $date_end = date("UTC",$end);
        $client = new Client();

        //start navigation
        $crawler = $client->request('GET', env("ERP_URL_HOME"));

        // select the form and fill in some values
        $form = $crawler->selectButton($this->config()['css_nextPage'])->form();
        $form['UserName'] = env("ERP_USER");
        $form['Password'] = env("ERP_PASSWORD");

        // submit that form
        $crawler = $client->submit($form);

        //Extract data
        $crawler = $client->request('GET', env("ERP_URL_REPORT"));
        $form = $crawler->selectButton('Pesquisar')->form();
        $form['dataInicio'] = $date_start;
        $form['dataFinal'] = $date_end;
        $crawler = $client->submit($form);

        $htmlTable = $crawler->filter($this->config()['css_tableResult'])->html();

        $uris = [];

        $next = $crawler->filter($this->config()['css_nextPage']);
        while ($next->count() > 0) {
            $uris[] = $next->link()->getUri();
        }

        return $crawler->filter($this->config()['css_tableResult'])->html();
    }
}
