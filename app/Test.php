<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        return self::testGetCustomersCredits();
    }

    public static function testGetCustomersCredits(){
        return Customer::getCustomersCredits();
    }

    public static function testImportERP(){
        $start = new \DateTime("2020-01-01");
        $end = new \DateTime("2020-01-31");

        $import = new ImportSalesToDB($start,$end);

        return $import->import();
    }
}
