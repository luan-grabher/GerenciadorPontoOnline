<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public static function getCustomersCredits(){
        return Customer::
            select('customers.cpf','customers.name', 'customers.birthday', 'customers.email','customers.lastSale')->
            selectRaw('sum(sale_items.creditAdded) as credit')->
            join('sale_items','customers.lastSale','=','sale_items.sale_id')->
            where([
                ['sale_items.creditAdded','>','0']
            ])->
            groupBy('customers.cpf','customers.name', 'customers.birthday', 'customers.email')->
            get();
    }



}
