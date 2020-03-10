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
        $date_start = date("Y-m-d H:i:s", ($start / 1000));
        $date_end = date("Y-m-d H:i:s", ($end / 1000));

        $client = new Client();

        //Log-in in system
        if (self::getJsonFromErp_Login($client)) {
            //Pick-up Row values
            return self::getJsonFromErp_PickupRows($client, $date_start, $date_end);
            //return "<pre>" . json_encode($rowValues, JSON_PRETTY_PRINT) . "</pre>";
        }
    }

    private static function getJsonFromErp_Login(Client $client): bool
    {
        try {
            $crawler = $client->request('GET', env("ERP_URL_HOME"));
            $form = $crawler->selectButton(self::config()['nameButtonLogin'])->form();
            $form['UserName'] = env("ERP_USER");
            $form['Password'] = env("ERP_PASSWORD");
            $crawler = $client->submit($form);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function getJsonFromErp_PickupRows(Client $client, string $date_start, string $date_end): array
    {
        $cols = [];
        try {
            $page = 0;
            $continue = true;
            while ($continue & $page < env("ERP_MAX_PAGES")) {
                $page++;
                $crawler = $client->request("POST", env("ERP_URL_REPORT") . "?dataInicio=$date_start&dataFinal=$date_end&page=$page");
                $rowsElements = $crawler->filter(self::config()['css_rows']);
                if ($rowsElements->count() > 0) {
                    //for each loop in rows
                    $rowsElements->each(function (Crawler $node, $i) use (&$cols) {
                        $colsElements = $node->filter(self::config()['css_colls']);

                        $cols[] = [
                            "matricula" => $colsElements->eq(0)->text(),
                            "tid" => $colsElements->eq(1)->text(),
                            "curso_id" => $colsElements->eq(2)->text(),
                            "aluno" => $colsElements->eq(4)->text(),
                            "curso" => $colsElements->eq(3)->text(),
                            "dataRecebimento" => $colsElements->eq(5)->text(),
                            "dataPagamento" => $colsElements->eq(6)->text(),
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
                            "valorAfiliados" => $colsElements->eq(19)->text()
                        ];
                    });
                } else {
                    $continue = false;
                }
            }
        } catch (\Exception $e) {
            //return ["Erro"=>$e->getMessage()];
        }
        return $cols;

    }

    public static function importDataFromERPToDatabase(int $start, int $end): array
    {
        $messages = new Messages();
        try {

            //Buscar dados da api
            $data = self::getJsonFromErp($start, $end);

            //imprimir dados da api
            foreach ($data as $vendaAPI) {
                $venda = ErpVenda::where(
                    [
                        ['tid', '=', $vendaAPI['tid']],
                        ['curso_id', '=', $vendaAPI['curso_id']],
                        ['matricula', '=', $vendaAPI['matricula']]
                    ])->first();

                $venda = !$venda == null ? $venda : new ErpVenda();

                $venda->matricula = $vendaAPI['matricula'];
                $venda->tid = $vendaAPI['tid']==""?0:$vendaAPI['tid'];
                $venda->curso_id = $vendaAPI['curso_id'];
                $venda->aluno = $vendaAPI['aluno'];
                $venda->curso = $vendaAPI['curso'];

                $dataRecebimento = new \DateTime($vendaAPI['dataRecebimento']);
                $dataPagamento = new \DateTime($vendaAPI['dataPagamento']);
                $venda->dataRecebimento = $dataRecebimento;
                $venda->dataPagamento = $dataPagamento;

                $venda->valorTotal = (int) filter_var($vendaAPI['valorTotal'], FILTER_SANITIZE_NUMBER_INT);
                $venda->valorCursoSD = (int) filter_var($vendaAPI['valorCursoSD'], FILTER_SANITIZE_NUMBER_INT);
                $venda->valorCursoCD = (int) filter_var($vendaAPI['valorCursoCD'], FILTER_SANITIZE_NUMBER_INT);
                $venda->metodoPagamento = $vendaAPI['metodoPagamento'];
                $venda->qxmat = $vendaAPI['qxmat'];
                $venda->soma = $vendaAPI['soma'];
                $venda->amais = $vendaAPI['amais'];
                $venda->credUtilizado = $vendaAPI['credUtilizado'];
                $venda->credAluno = $vendaAPI['credAluno'];
                $venda->pgamenos = (int) filter_var($vendaAPI['pgamenos'] ."0",FILTER_SANITIZE_NUMBER_INT);
                $venda->cupom = $vendaAPI['cupom'];
                $venda->afiliados = $vendaAPI['afiliados'];
                $venda->valorAfiliados = (int) filter_var($vendaAPI['valorAfiliados'], FILTER_SANITIZE_NUMBER_INT);

                $venda->save();
            }

            $messages->add("Importação concluída! Operações importadas/atualizadas: " . sizeof($data), 'success');

        } catch (\Exception $e) {
            //Exibir erro
            $messages->add('Ocorreu um erro desconhecido: ' . $e->getMessage(), 'danger');
        }

        return $messages->getArray();
    }
}
