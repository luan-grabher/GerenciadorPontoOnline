<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public static function getProfits(\Datetime $startDateSale, \Datetime $endDateSale){
        /*SELECT
        sum(sale_items.value - sale_items.discount - sale_items.creditUsed - sale_items.creditAdded - sale_items.reversed) as lucro,
        products.*
        FROM products
        inner join sale_items
        on sale_items.product_id = products.id
        group by products.id*/

        
    }
}
