<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected static array $status = [
        'canceled' => 'Cancelado',
        'active' => 'Ativo'
    ];

    public static function getProfits(\Datetime $startDateSale, \Datetime $endDateSale)
    {
        $profits = self::
        select('products.id as product', 'products.name', 'products.value', 'products.dateStart', 'products.dateEnd', 'products.status', 'products.type')->
        selectRaw(
            'sum(sale_items.value - sale_items.discount - sale_items.creditUsed - sale_items.creditAdded - sale_items.reversed) as profit, '
            . 'sum(sale_items.value)  as totalSalesValue, '
            . 'sum(sale_items.discount) as totalSalesDiscount, '
            . 'sum(sale_items.creditUsed) as totalSalesCreditUsed, '
            . 'sum(sale_items.creditAdded) as totalSalesCreditAdded, '
            . 'sum(sale_items.reversed) as totalSalesReversed'
        )->
        join('sale_items', 'sale_items.product_id', '=', 'products.id')->
        join('sales', 'sale_items.sale_id', '=', 'sales.id')->
        where([
            ['sales.date', '>=', $startDateSale->format('Y-m-d')],
            ['sales.date', '>=', $endDateSale->format('Y-m-d')]
        ])->
        groupBy('products.id')->
        orderBy('products.dateStart')->
        get();

        foreach ($profits as $i => $profit){
            #try{
                $teachers = ProductTeacher::select('*')->where('product_id',$profit['product'])->get()->toArray();


                $classes = array_sum (array_column ((array)$teachers, 'classes'));
                try {
                    $valueOneClass = $profit['profit'] / $classes;
                }catch(\Exception $e){
                }

                $totalTeachers = 0;
                foreach ($teachers as $teacher){
                    #try{
                        $totalTeachers += ($teacher['classes'] * $valueOneClass)/($teacher['percent']/100);
                    #}catch(\Exception $e){
                    #}
                }

                $profits[$i]['valueTeachers'] = $totalTeachers;
                $profits[$i]['valueToAppropriate'] = $profit['profit'] - $totalTeachers;
            #}catch(\Exception $e){
            #}
        }

        return $profits;
    }

    public static function getCanceledProducts(\Datetime $startDateSale, \Datetime $endDateSale)
    {
        return self::
        select('products.id', 'products.name', 'products.value', 'products.dateStart', 'products.dateEnd', 'products.status', 'products.type')->
        where([
            ['products.dateStart', '>=', $startDateSale->format('Y-m-d')],
            ['products.dateEnd', '>=', $endDateSale->format('Y-m-d')],
            ['products.status', '=', self::$status['canceled']]
        ])->
        groupBy('products.id')->
        orderBy('products.dateStart')->
        get();
    }

    public static function getStudents(\Datetime $startDateSale, \Datetime $endDateSale)
    {
        return self::
        select(
            'sale_items.product_id', 'products.name', 'products.dateStart', 'products.dateEnd', 'products.dateUnavailability', 'products.status'
        )->selectRaw(
            "count(if(sale_items.status = '". self::$status['active'] ."',1,NULL)) as active, " .
            "count(if(sale_items.status <> '". self::$status['active'] ."',1,NULL)) as canceled, " .
            "count(sale_items.status) as subscribers"
        )->join(
            'sale_items','products.id','sale_items.product_id'
        )->join(
            'sales','sales.id','sale_items.sale_id'
        )->where([
            ['sales.date','>=',$startDateSale],
            ['sales.date','<=',$endDateSale]
        ])->groupBy(
            'product_id'
        )->get();
    }
}
