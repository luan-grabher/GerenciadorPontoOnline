<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\DomCrawler\Crawler;

class ERP_Importation extends Model
{
    private array $config = [
        "css" => [
            "login" => [
                "btn" => "Entrar"
            ],
            "sales" => [
                "table" => [
                    "rows" => "tr",
                    "cols" => "td",
                    "nextPage" => ".PagedList-skipToNext a"
                ]
            ],
            "sale" => [
                'value' => "div#blockUI dd:nth-child(12)",
                'date' => "div#blockUI dd:nth-child(6)",
                'paymentDate' => "input#DataPagamento",
                'tid' => "div#blockUI dd:nth-child(14)",
                'paymentMethod' => 'div#blockUI dd:nth-child(16)',
                'installments' => 'div#blockUI dd:nth-child(18)',
                'canceled' => 'input#Cancelado',
                'justificationCancellation' => 'input#JustificativaCancelamento',
                'creditUsed' => 'div#ItensPedido tr:nth-child(2) > td',
                'customer' => [
                    'cpf' => 'div#DadosCliente tr:nth-child(2) > td:nth-child(2)',
                    'name' => 'div#DadosCliente tr:nth-child(1) > td:nth-child(2)',
                    'birthDay' => 'div#DadosCliente tr:nth-child(3) > td:nth-child(2)',
                    'email' => 'div#DadosCliente tr:nth-child(4) > td:nth-child(2)'
                ],
                'items' => [
                    'rows' => 'div#ItensPedido tr',
                    'cols' => 'td',
                    'productLink' => 'a'
                ]
            ],
            'product' => [
                'name' => 'input#Nome',
                'value' => 'input#Preco',
                'dateStart' => 'input#DataInicioCurso',
                'dateEnd' => 'input#DataFimCurso',
                'status' => 'select#Status',
                'type' => 'select#TipoProdutoId',
                'dateUnavailability' => 'input#DataIndisponivelAluno',
                'selectValue' => 'option[selected=selected]',
                'teachers' => 'div#ProdutoProfessorView tr'
            ],
            'any' => [
                'rows' => 'tr',
                'cols' => 'td',
                'selected' => 'option[selected=selected]'
            ]
        ]
    ];

    private \DateTime $dateStart;
    private \DateTime $dateEnd;
    private Client $client;

    private array $sales = [];
    private array $products = [];
    private array $customers = [];

    /**
     * ImportERP constructor.
     * @param $dateStart \DateTime format Y-m-d
     * @param $dateEnd \DateTime format Y-m-d
     */
    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    public function import(): array
    {
        ini_set('max_execution_time', -1);
        $this->client = new Client();

        $run = [];
        /*Faz Login*/
        if (!isset(($run = $this->erp_makeLogin())['error'])) {
            /*Pega o id de todas as vendas do periodo*/
            if (!isset(($run = $this->erp_getSalesNumber())['error'])) {
                /*Pega as informações das vendas uma a uma*/
                if (!isset(($run = $this->erp_getInfoSales())['error'])) {
                    /*Cria lista de produtos usando as vendas*/
                    if (!isset(($run = $this->erp_createProductList())['error'])) {
                        /*Pega as informações dos produtos da lista*/
                        if (!isset(($run = $this->erp_getInfoProducts())['error'])) {
                            $run = [
                                'sales' => $this->sales,
                                'products' => $this->products,
                                'customers' => $this->customers
                            ];
                        }
                    }
                }
            }
        }

        return $run;
    }

    private function erp_makeLogin()
    {
        try {

            $crawler = $this->client->request('GET', env("ERP_URL_LOGIN"));

            $error = "Fez o request!";

            $form = $crawler->selectButton($this->config['css']['login']['btn'])->form();
            $form['UserName'] = env("ERP_USER");
            $form['Password'] = env("ERP_PASSWORD");
            $crawler = $this->client->submit($form);

            /*Se a url que for depois do submit for a principal, fez login, se for igual a url de login, não fez o login*/
            if($crawler->getUri() == env("ERP_URL_HOME") . "/"){
                return [];
            }else{
                throw new \Exception("Não foi possivel realizar o login com o usuário e senha fornecidos nas configurações!");
            }
        } catch (\Exception $e) {
            return [
                "error" => "Erro ao fazer login: (" . $e->getCode() . ")" . $e->getMessage()
            ];
        }
    }

    private function erp_getSalesNumber()
    {
        try {
            $page = (int)0;
            $next = (bool)true;

            while ($next) {
                $page++;
                $response = $this->client->request(
                    "POST",
                    env("ERP_URL_FILTRAPEDIDO") . "?page=$page",
                    [
                        "DataInicioPedido" => $this->dateStart->format('Y-m-d H:i:s'),
                        "DataFImPedido" => $this->dateEnd->format('Y-m-d H:i:s')
                    ]
                );

                //Get numbers of sales
                $rows = $response->filter($this->config['css']['sales']['table']['rows']);
                foreach ($rows as $row) {
                    $row = new Crawler($row);
                    $cols = $row->filter($this->config['css']['sales']['table']['cols']);
                    if ($cols->count() > 0) {
                        $this->sales[]['saleNumber'] = $cols->first()->text();
                    }
                }

                $next = (bool)$response->filter($this->config['css']['sales']['table']['nextPage'])->count();
            }

            return $this->sales;
        } catch (\Exception $e) {
            return [
                "error" => "Erro ao encontrar vendas: " . $e->getMessage()
            ];
        }
    }

    private function erp_getInfoSales()
    {
        try {
            foreach ($this->sales as &$sale) {
                $response = $this->client->request(
                    "GET",
                    env("ERP_URL_PEDIDOEDIT") . "/" . $sale['saleNumber']
                );

                $sale['profit'] = $response->filter($this->config['css']['sale']['value'])->text();
                $sale['date'] = $response->filter($this->config['css']['sale']['date'])->text();
                $sale['paymentDate'] = $response->filter($this->config['css']['sale']['paymentDate'])->attr('value');
                $sale['tid'] = $response->filter($this->config['css']['sale']['tid'])->text();

                $paymentMethod = $response->filter($this->config['css']['sale']['paymentMethod']);
                $sale['paymentMethod'] = strpos($paymentMethod->html(), 'select') === false ? $paymentMethod->text() : "Boleto *";

                $sale['installments'] = $response->filter($this->config['css']['sale']['installments'])->text();
                $sale['canceled'] = (bool)$response->filter($this->config['css']['sale']['canceled'])->attr('checked');
                $sale['justificationCancellation'] = $response->filter($this->config['css']['sale']['justificationCancellation'])->attr('value');
                $sale['creditUsed'] = $response->filter($this->config['css']['sale']['creditUsed'])->text();

                $sale['customer'] = $this->erp_getSaleCustomer($response, $sale['saleNumber']);

                $sale['items'] = $this->erp_getSalesItems($response->filter($this->config['css']['sale']['items']['rows']));
            }


            return $this->sales;
        } catch (\Exception $e) {
            return [
                "error" => "Erro ao pegar informações das vendas: " . $e->getMessage()
            ];
        }
    }

    private function erp_getSaleCustomer(Crawler $response, int $sale)
    {
        $customer = [];
        $customer['cpf'] = $response->filter($this->config['css']['sale']['customer']['cpf'])->text();
        $customer['name'] = $response->filter($this->config['css']['sale']['customer']['name'])->text();
        $customer['birthday'] = $response->filter($this->config['css']['sale']['customer']['birthDay'])->text();
        $customer['email'] = $response->filter($this->config['css']['sale']['customer']['email'])->text();
        $customer['sale'] = $sale;

        //If not exists this customer in this import or is most new
        if (
            !isset($this->customers[$customer['cpf']]) ||
            (
                isset($this->customers[$customer['cpf']]) &&
                $this->customers[$customer['cpf']]['sale'] < $customer['sale']
            )
        ) {
            $this->customers[$customer['cpf']]['cpf'] = $customer['cpf'];
            $this->customers[$customer['cpf']]['name'] = $customer['name'];
            $this->customers[$customer['cpf']]['birthday'] = $customer['birthday'];
            $this->customers[$customer['cpf']]['email'] = $customer['email'];
            $this->customers[$customer['cpf']]['sale'] = $customer['sale'];
        }

        return $customer;
    }

    private function erp_getSalesItems(Crawler $rows)
    {
        $items = [];
        foreach ($rows as $item) {
            $item = new Crawler($item);

            $cols = $item->filter($this->config['css']['sale']['items']['cols']);
            if ($cols->count() && is_numeric($cols->first()->text())) {
                $values = $this->erp_getSaleItemValues( $cols->eq(3)->html());

                $items[] = [
                    'product' => (int)$cols->eq(0)->text(),
                    'status' => $cols->eq(2)->text(),
                    'value' => $values['value'],
                    'discount' => $values['discount'],
                    'creditAdded' => $values['creditAdded'],
                    'creditUsed' => $values['creditUsed'],
                    'reversed' => $values['reversed'],
                    'description' => $values['description'],
                    'valueElement' => $values
                ];
            }
        }
        return $items;
    }

    private function erp_getSaleItemValues(string $colHtml)
    {
        $values = [
            'value' => 0,
            'discount' => 0,
            'creditAdded' => 0,
            'creditUsed' => 0,
            'reversed'  => 0,
            'description' => '',
        ];

        $valuesHtml = explode("<br>", str_replace('  ', '', str_replace("\r\n", "", $colHtml)));

        $values['value'] = $this->erp_getValueFromMoney($valuesHtml[0]);
        if(sizeof($valuesHtml) > 1){
            foreach ($valuesHtml as $val){
                if(strpos($val,'creditado')){
                    $values['creditAdded'] = $this->erp_getValueFromMoney($val);
                }elseif(strpos($val,'credito')){
                    $values['creditUsed'] = $this->erp_getValueFromMoney($val);
                }elseif(strpos($val,'desconto')){
                    $values['discount'] = $this->erp_getValueFromMoney($val);
                }elseif(strpos($val,'estornado')){
                    $values['reversed'] = $this->erp_getValueFromMoney($val);
                }elseif($val != $valuesHtml[0]){
                    $values['description'] .= ($values['description']==''?'':' ') . $val;
                }
            }
        }

        return $values;
    }

    private function erp_getValueFromMoney(string $money): float{
        return (float)($money != null ? ((int)filter_var($money, FILTER_SANITIZE_NUMBER_FLOAT)) / 100 : 0);
    }

    private function erp_createProductList()
    {
        try {
            foreach ($this->sales as $sale) {
                foreach ($sale['items'] as $item) {
                    $product = $item['product'];
                    if (!array_key_exists($product, $this->products)) {
                        $this->products[$product] = [
                            'code' => $product
                        ];
                    }
                }
            }

            return $this->products;
        } catch (\Exception $e) {
            return [
                'error' => "Erro ao criar lista de cursos das vendas: " . $e->getMessage()
            ];
        }
    }

    private function erp_getInfoProducts() : array
    {
        try {
            foreach ($this->products as &$product) {
                $response = $this->client->request(
                    "GET",
                    env("ERP_URL_PRODUTO") . "/" . $product['code']
                );

                $product['name'] = $response->filter($this->config['css']['product']['name'])->attr('value');
                $product['value'] = (float)$response->filter($this->config['css']['product']['value'])->attr('value');
                $product['dateStart'] = $response->filter($this->config['css']['product']['dateStart'])->attr('value');
                $product['dateEnd'] = $response->filter($this->config['css']['product']['dateEnd'])->attr('value');
                $product['status'] = $response->filter($this->config['css']['product']['status'])->filter($this->config['css']['product']['selectValue'])->text();
                $product['type'] = $response->filter($this->config['css']['product']['type'])->filter($this->config['css']['product']['selectValue'])->text();
                $product['dateUnavailability'] = $response->filter($this->config['css']['product']['dateUnavailability'])->attr('value');

                $product['teachers'] = $this->erp_getProductTeachers($response->filter($this->config['css']['product']['teachers']));
            }

            return $this->products;
        } catch (\Exception $e) {
            return [
                'error' => 'Erro ao pegar informações dos cursos(' . $e->getLine() . '): ' . $e->getMessage() . $e->getTraceAsString()
            ];
        }
    }

    /**
     * @return array Lista de Professores dos cursos
     */
    private function erp_getProductTeachers(Crawler $rows) : array
    {
        $teachers = [];
        foreach ($rows as $row) {
            $row = new Crawler($row);

            $cols = $row->filter($this->config['css']['any']['cols']);
            if ($cols->count() > 0) {
                $teachers[] = [
                    'name' => $cols->eq(0)->text(),
                    'percent' => (float)$cols->eq(1)->text(),
                    'classes' => (float)$cols->eq(2)->text()
                ];
            }
        }
        return $teachers;
    }
}
