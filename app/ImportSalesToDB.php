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
                if(!isset(($run = $this->importSales())['error'])){

                }
            }
        }

        return $run;
    }

    public function importSales(){
        try {
            if(isset($this->data['sales'])){
                $sales = $this->data['sales'];

            }else{
                throw new \Exception('Sales is not set from Import ERP');
            }
            return $this->imported;
        }catch (\Exception $e){
            return ArrayError::error('Import Sales to DB',$e);
        }
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
                    $this->imported['products'][$productERP['code']]['code'] = $productERP['code'];

                    //Teachers
                    $this->imported['products'][$productERP['code']]['teachers'] =
                        $this->importProductTeachers($productERP['code'],$productERP['teachers']);
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
            $importedTeachers = [];

            foreach ($teachers as $teacherERP){
                $teacher = ProductTeacher::where([
                    ['product_id',$product],
                    ['name',$teacherERP['name']]
                ])->first();
                $teacher = !is_null($teacher)?$teacher:new ProductTeacher();

                $teacher->product_id = $product;
                $teacher->name = $teacherERP['name'];
                $teacher->percent = $teacherERP['percent'];
                $teacher->classes = $teacherERP['classes'];

                $teacher->save();

                $importedTeachers[] = $teacherERP['name'];
            }

            return $importedTeachers;
        }catch(\Exception $e){
            return ArrayError::error('Import Product Teachers to DB',$e);
        }
    }
}
