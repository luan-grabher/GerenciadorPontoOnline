<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where([
            ['product_id',$this->product_id],
            ['sale_id',$this->sale_id]
        ]);

        return $query;
    }
}
