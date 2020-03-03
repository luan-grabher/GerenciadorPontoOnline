<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\DomCrawler\Crawler;

class ErpVenda extends Model
{
    private static function config()
    {
        return [
            "css_nextPage" => ".PagedList-skipToNext a",
            "nameButtonLogin" => "Entrar",
            "nameButtonSearch" => "Pesquisar",
            "css_tableResult" => "#partialPedidoMensal",
            "css_rows" => ".odd.gradeX",
            "css_colls" => "td"
        ];
    }

    public static function getJsonFromErp(int $start, int $end)
    {
        //Prepare function
        $date_start = date("Y-m-d H:i:s",($start/1000));
        $date_end = date("Y-m-d H:i:s",($end/1000));

        $client = new Client();

        //Log-in in system
        if(self::getJsonFromErp_Login($client)){
            //Pick-up Row values
            $rowValues = self::getJsonFromErp_PickupRows($client,$date_start,$date_end);
            return "<pre>" . json_encode($rowValues, JSON_PRETTY_PRINT) . "</pre>";
        }
    }

    private static function getJsonFromErp_Login(Client $client):bool{
        try {
            $crawler = $client->request('GET', env("ERP_URL_HOME"));
            $form = $crawler->selectButton(self::config()['nameButtonLogin'])->form();
            $form['UserName'] = env("ERP_USER");
            $form['Password'] = env("ERP_PASSWORD");
            $crawler = $client->submit($form);

            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    private static function getJsonFromErp_PickupRows(Client $client, string $date_start, string $date_end):array {
        try {
            $cols = [];

            $page = 0;
            $continue = true;
            while ($continue & $page < env("ERP_MAX_PAGES")){
                $page++;
                $crawler = $client->request("POST",env("ERP_URL_REPORT") . "?dataInicio=$date_start&dataFinal=$date_end&page=$page");
                $rowsElements = $crawler->filter(self::config()['css_rows']);
                if($rowsElements->count() > 0){
                    //for each loop in rows
                    $rowsElements->each(function(Crawler $node,$i) use (&$cols){
                        $colsElements = $node->filter(self::config()['css_colls']);

                        $cols[] = [
                            "matricula" => $colsElements->eq(0)->text(),
                            "tid"=> $colsElements->eq(1)->text(),
                            "curso_id"=> $colsElements->eq(2)->text(),
                            "aluno"=> $colsElements->eq(4)->text(),
                            "curso"=> $colsElements->eq(3)->text(),
                            "dataRecebimento"=> $colsElements->eq(5)->text(),
                            "dataPagamento"=> $colsElements->eq(6)->text(),
                            "valorTotal" => $colsElements->eq(7)->text(),
                            "valorCursoSD" => $colsElements->eq(8)->text(),
                            "valorCursoCD" => $colsElements->eq(9)->text(),
                            "metodoPagamento" => $colsElements->eq(10)->text(),
                            "qxmat" => $colsElements->eq(11)->text(),
                            "soma" => $colsElements->eq(12)->text(),
                            "amais" => $colsElements->eq(13)->text(),
                            "credUtilizado" => $colsElements->eq(14)->text(),
                            "credAluno" => $colsElements->eq(15)->text(),
                            "pgamenos" => $colsElements->eq(16)->text(),
                            "cupom" => $colsElements->eq(17)->text(),
                            "afiliados" => $colsElements->eq(18)->text(),
                            "valorAfiliado" => $colsElements->eq(19)->text()
                        ];
                    });
                }else{
                    $continue = false;
                }
            }

            return $cols;
        }catch (\Exception $e){
            return ["Erro"=>$e->getMessage()];
        }
    }
}
