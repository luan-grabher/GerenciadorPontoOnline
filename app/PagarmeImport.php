<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PagarMe\Client;

class PagarmeImport extends Model
{
    protected int $page = 0;
    protected int $maxPageObjects = 1000;

    protected string $objectType;
    protected array $filter;
    protected array $objectAttributes;
    protected array $results = [];

    /**
     * PagarmeImport constructor.
     * @param string $objectType
     * @param array $filter
     * @param array $objectAttributes
     */
    public function __construct(string $objectType, array $filter, array $objectAttributes)
    {
        $this->objectType = $objectType;

        $this->filter = $filter;
        $this->filter['count'] = $this->maxPageObjects;
        $this->filter['page'] = &$this->page;

        $this->objectAttributes = $objectAttributes;
    }

    public function import(){
        ini_set ('max_execution_time',  -1);

        //Inicializa Client Pagarme
        $client = new Client(env('PAGARME_API_KEY'));
        $objectType = $this->objectType;

        //Enquanto não ocorrer erros, continue buscando
        while ($this->page<env("PAGARME_MAX_PAGES")){
            try {
                $this->page++;
                //$this->filter['page'] = $this->page;

                $resultsOfPage = $client->$objectType()->getList($this->filter);

                if(sizeof($resultsOfPage) > 0){
                    foreach ($resultsOfPage as $r){
                        try {
                            $add = [];
                            foreach ($this->objectAttributes as $objectAttribute){
                                if($objectAttribute instanceof PagarmeObjectAttribute){
                                    $vectorValue = self::getVector($r,$objectAttribute->getRouteFor());
                                    $valueForObject = $vectorValue != null && !is_array($vectorValue) && !is_object($vectorValue)?$vectorValue:$objectAttribute->getDefault();

                                    $add[$objectAttribute->getName()] = $valueForObject;
                                }
                            }
                            $this->results[] = $add;
                        }catch (\Exception $e){
                        }
                    }
                }else{
                    break;
                }
            }catch (\Exception $e){
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public static function getVector(object $arrayOriginal, array $routeVectors){
        $value = $arrayOriginal;

        foreach ($routeVectors as $routeVector){
            if(isset($value->$routeVector)){
                $value = $value->$routeVector;
            }
        }

        return $value;
    }
}
