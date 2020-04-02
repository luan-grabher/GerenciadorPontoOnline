<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportSalesToDB extends Model
{
    private \DateTime $dateStart;
    private \DateTime $dateEnd;

    private array $data;
    private array $imported = ['products'=>[],'sales'=>[]];

    /**
     * ImportSalesToDB constructor.
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     */
    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;

        $ERPImport = new ImportSalesFromEPR($this->dateStart,$this->dateEnd);

        $this->data = $ERPImport->import();
    }

    /**
     * @return array
     */
    public function getImported(): array
    {
        return $this->imported;
    }


    public function import(){
        $run = $this->data;
        if(!isset($run['error'])){
            if(!isset(($run = $this->importProducts())['error'])){

            }
        }

        return $run;
    }

    public function importProducts():array{
        try {
            if(isset($this->data['products'])){
                $products = $this->data['products'];
                foreach ($products as $productERP){
                    $product = Product::where($productERP['code'])->first();
                    $product = ! $product == null? $product : new Product();

                    $product->id = $productERP['code'];
                    $product->name = $productERP['name'];
                    $product->value = $productERP['value'];
                    $product->dateStart = $productERP['dateStart'];
                    $product->dateEnd = $productERP['dateEnd'];
                    $product->status = $productERP['status'];
                    $product->type = $productERP['type'];
                    $product->dateUnavailability = $productERP['dateUnavailability'];

                    $product->save();
                    $this->imported['products'][$product['code']] = $product['code'];
                }
            }else{
                throw new \Exception("Products is not set in importation of ERP.");
            }

            return $this->imported;
        }catch(\Exception $e){
            return [
                'error' =>  "Import Products to DB: " . $e->getMessage()
            ];
        }
    }

}
