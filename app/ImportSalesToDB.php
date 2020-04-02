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
                //return $run;
            }
        }

        return $run;
    }

    public function importProducts():array{
        try {
            if(isset($this->data['products'])){
                $products = $this->data['products'];
                foreach ($products as $productERP){
                    $product = Product::where('id',$productERP['code'])->first();
                    $product = ! $product == null? $product : new Product();

                    $product->id = $productERP['code'];
                    $product->name = $productERP['name'];
                    $product->value = $productERP['value'];
                    $product->dateStart = \DateTime::createFromFormat('d/m/Y H:i:s',$productERP['dateStart'])->format('Y-m-d');
                    $product->dateEnd = \DateTime::createFromFormat('d/m/Y H:i:s',$productERP['dateEnd'])->format('Y-m-d');
                    $product->status = $productERP['status'];
                    $product->type = $productERP['type'];
                    $product->dateUnavailability = \DateTime::createFromFormat('d/m/Y H:i:s',$productERP['dateUnavailability'])->format('Y-m-d');

                    $product->save();
                    $this->imported['products'][$productERP['code']] = $productERP['code'];
                }
            }else{
                throw new \Exception("Products is not set in importation of ERP.");
            }

            return $this->imported;
        }catch(\Exception $e){
            return ArrayError::error('Import Product to DB', $e);
        }
    }
    public function importProductTeachers(int $product, array  $teachers){
        try {

        }catch(\Exception $e){
            return ArrayError::error('Import Product Teachers to DB',$e);
        }
    }
}
