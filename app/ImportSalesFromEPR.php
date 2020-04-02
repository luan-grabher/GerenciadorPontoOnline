<?php

namespace App;

use Goutte\Client;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\DomCrawler\Crawler;

class ImportSalesFromEPR extends Model
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
                    'birthDate' => 'div#DadosCliente tr:nth-child(3) > td:nth-child(2)',
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

    /**
     * ImportERP constructor.
     * @param $dateStart \DateTime format Y-m-d
     * @param $dateEnd \DateTime format Y-m-d
     */
    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;

        $this->client = new Client();
    }

    public function import(): array
    {
        ini_set('max_execution_time', -1);

        $run = [];
        if (!isset(($run = $this->makeLogin())['error'])) {
            if (!isset(($run = $this->getSalesNumber())['error'])) {
                if (!isset(($run = $this->getInfoSales())['error'])) {
                    if (!isset(($run = $this->createProductList())['error'])) {
                        if (!isset(($run = $this->getInfoProducts())['error'])) {
                            $run = [
                                'sales'=>  $this->sales,
                                'products' => $this->products
                            ];
                        }
                    }
                }
            }
        }

        return $run;
    }

    /**
     * @return array
     */
    public function getSales(): array
    {
        return $this->sales;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    private function makeLogin()
    {
        try {
            $crawler = $this->client->request('GET', env("ERP_URL_HOME"));
            $form = $crawler->selectButton($this->config['css']['login']['btn'])->form();
            $form['UserName'] = env("ERP_USER");
            $form['Password'] = env("ERP_PASSWORD");
            $crawler = $this->client->submit($form);

            return [];
        } catch (\Exception $e) {
            return [
                "error" => $e->getMessage()
            ];
        }
    }

    private function getSalesNumber()
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
                "error" => "getSalesNumber: " . $e->getMessage()
            ];
        }
    }

    private function getInfoSales()
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

                $sale['customer']['cpf'] = $response->filter($this->config['css']['sale']['customer']['cpf'])->text();
                $sale['customer']['name'] = $response->filter($this->config['css']['sale']['customer']['name'])->text();
                $sale['customer']['birthDate'] = $response->filter($this->config['css']['sale']['customer']['birthDate'])->text();
                $sale['customer']['email'] = $response->filter($this->config['css']['sale']['customer']['email'])->text();

                $sale['items'] = $this->getSalesItems($response->filter($this->config['css']['sale']['items']['rows']));
            }


            return $this->sales;
        } catch (\Exception $e) {
            return [
                "error" => "getInfoSales: " . $e->getMessage()
            ];
        }
    }

    private function getSalesItems(Crawler $rows)
    {
        $items = [];
        foreach ($rows as $item) {
            $item = new Crawler($item);

            $cols = $item->filter($this->config['css']['sale']['items']['cols']);
            if ($cols->count() && is_numeric($cols->first()->text())) {
                $colValue = explode(
                    "<br>",
                    str_replace('  ',
                        '',
                        str_replace(
                            "\r\n", "",
                            $cols->eq(3)->html()
                        )
                    )
                );

                $items[] = [
                    'product' => (int)$cols->eq(0)->text(),
                    'status' => $cols->eq(2)->text(),
                    'value' => (float)(isset($colValue[0]) ? ((int)filter_var($colValue[0], FILTER_SANITIZE_NUMBER_FLOAT)) / 100 : 0),
                    'discount' => (float)(isset($colValue[1]) ? ((int)filter_var($colValue[1], FILTER_SANITIZE_NUMBER_FLOAT)) / 100 : 0),
                    'description' => isset($colValue[2]) ? $colValue[2] : '',
                    'valueElement' => $colValue
                ];
            }
        }
        return $items;
    }

    private function createProductList()
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
                'error' => "createProductlist: " . $e->getMessage()
            ];
        }
    }

    private function getInfoProducts()
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

                $product['teachers'] = $this->getProductTeachers($response->filter($this->config['css']['product']['teachers']));
            }

            return $this->products;
        } catch (\Exception $e) {
            return [
                'error' => 'getProducts(' . $e->getLine() . '): ' . $e->getMessage() . $e->getTraceAsString()
            ];
        }
    }

    private function getProductTeachers(Crawler $rows)
    {
        $teachers = [];
        foreach ($rows as $row) {
            $row = new Crawler($row);

            $cols = $row->filter($this->config['css']['any']['cols']);
            if ($cols->count() > 0) {
                $teachers[] = [
                    'name' => $cols->eq(0)->text(),
                    'percent' => (float) $cols->eq(1)->text(),
                    'classes' => (float) $cols->eq(2)->text()
                ];
            }
        }
        return $teachers;
    }
}
