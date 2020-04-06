<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportSalesToDB extends Model
{
    private \DateTime $dateStart;
    private \DateTime $dateEnd;

    private array $data;
    private array $imported = ['products'=>[],'customers'=>[],'sales'=>[]];

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
                if(!isset(($run = $this->importCustomers())['error'])){
                    if(!isset(($run = $this->importSales())['error'])){

                    }
                }
            }
        }

        return $run;
    }

    public function importCustomers(){
        try {
            if(isset($this->data['customers'])){
                $customers = $this->data['customers'];
                foreach ($customers as $customerERP){
                    $customer = Customer::where('cpf',$customerERP['cpf'])->first();

                    if(is_null($customer) || (!is_null($customer) && $customer->lastSale < $customerERP['sale'])){
                        $customer = !is_null($customer)?$customer:new Customer();

                        $customer->cpf = $customerERP['cpf'];
                        $customer->name = $customerERP['name'];
                        $customer->birthday= \DateTime::createFromFormat('d/m/Y', $customerERP['birthday'])->format('Y-m-d');
                        $customer->email = $customerERP['email'];
                        $customer->lastSale = $customerERP['sale'];

                        $customer->save();

                        $this->imported['customers'][$customer->cpf] = $customer->name;
                    }
                }
            }else{
                throw new \Exception('Customers is not set from Import ERP');
            }
            return $this->imported;
        }catch (\Exception $e){
            return ArrayError::error('Import Customers to DB',$e);
        }
    }

    public function importSales(){
        try {
            if(isset($this->data['sales'])){
                $sales = $this->data['sales'];
                foreach ($sales as $saleERP){
                    $sale = Sale::where('id',$saleERP['saleNumber'])->first();
                    $sale = !is_null($sale)?$sale:new Sale();

                    $sale->id = $saleERP['saleNumber'];
                    $sale->profit = (float)((int)filter_var($saleERP['profit'], FILTER_SANITIZE_NUMBER_FLOAT)) / 100;
                    $sale->date = \DateTime::createFromFormat('d/m/Y H:i:s', $saleERP['date'])->format('Y-m-d');
                    $sale->paymentDate = \DateTime::createFromFormat('d/m/Y H:i:s', $saleERP['paymentDate'])->format('Y-m-d');
                    $sale->tid = $saleERP['tid']==""?0:$saleERP['tid'];
                    $sale->paymentMethod = $saleERP['paymentMethod'];
                    $sale->installments = $saleERP['installments'];
                    $sale->canceled = $saleERP['canceled'];
                    $sale->justificationCancellation = $saleERP['justificationCancellation'];
                    $sale->creditUsed = (float)((int)filter_var($saleERP['creditUsed'], FILTER_SANITIZE_NUMBER_FLOAT)) / 100;
                    $sale->customer_cpf = $saleERP['customer']['cpf'];

                    $sale->save();

                    $this->imported['sales'][$sale->id] = [
                        'id' => $sale->id,
                        'items' => $this->importSaleItems($saleERP)
                    ];
                }
            }else{
                throw new \Exception('Sales is not set from Import ERP');
            }
            return $this->imported;
        }catch (\Exception $e){
            return ArrayError::error('Import Sales to DB',$e);
        }
    }

    public function importSaleItems(array $saleERP){
        try {
            $imported = [];

            foreach ($saleERP['items'] as $item){
                $saleItem = SaleItem::where([['product_id',$item['product']],['sale_id',$saleERP['saleNumber']]])->first();
                $saleItem = !is_null($saleItem)?$saleItem:new SaleItem();

                $saleItem->product_id = $item['product'];
                $saleItem->sale_id = $saleERP['saleNumber'];
                $saleItem->status = $item['status'];
                $saleItem->value = $item['value'];
                $saleItem->discount = $item['discount'];
                $saleItem->creditUsed = $item['creditUsed'];
                $saleItem->creditAdded = $item['creditAdded'];
                $saleItem->reversed = $item['reversed'];
                $saleItem->description = $item['description'];
                $saleItem->save();

                $imported[$saleItem->product_id] = $saleItem->product_id;
            }

            return $imported;
        }catch(\Exception $e){
            return ArrayError::string('Import Sale Items to DB',$e->getMessage(). " - " . $e->getTraceAsString());
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
