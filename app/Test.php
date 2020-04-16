<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        $start = new \DateTime("2020-01-01");
        $end = new \DateTime("2020-01-31");

        return Product::getProfits($start,$end);
    }
}
