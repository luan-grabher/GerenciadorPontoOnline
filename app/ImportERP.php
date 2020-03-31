<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class ImportERP extends Model
{
    private $config = [
        "css_nextPage" => ".PagedList-skipToNext a",
        "nameButtonLogin" => "Entrar",
        "nameButtonSearch" => "Pesquisar",
        "css_tableResult" => "#partialPedidoMensal",
        "css_rows" => ".odd.gradeX",
        "css_colls" => "td"
    ];
    private string $data_inicio;
    private string $data_fim;
    private Client $client;



    /**
     * ImportERP constructor.
     * @param $data_inicio string format Y-m-d H:i:s
     * @param $data_fim string format Y-m-d H:i:s
     */
    public function __construct(string $data_inicio,string $data_fim)
    {
        $this->data_inicio = $data_inicio;
        $this->data_fim = $data_fim;

        $client = new Client();
    }

    public function import() : array
    {
        if($this->makeLogin()){
            $this->getPedidos();
        }else{
            return ['error'=>'Error in login, the user and password is correct?'];
        }
    }


    private function makeLogin(): bool
    {
        try {
            $crawler = $this->client->request('GET', env("ERP_URL_HOME"));
            $form = $crawler->selectButton(self::config()['nameButtonLogin'])->form();
            $form['UserName'] = env("ERP_USER");
            $form['Password'] = env("ERP_PASSWORD");
            $crawler = $this->client->submit($form);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    private function getPedidos(){
        try {
            $response = $this->client->request(
                "POST",
                env("ERP_URL_REPORT") ,
                [
                    "DataInicioPedido" => $this->data_inicio,
                    "DataFImPedido" => $this->data_fim
                ]
            );
            return response;
        }catch(\Exception $e){
            return [];
        }
    }
}
