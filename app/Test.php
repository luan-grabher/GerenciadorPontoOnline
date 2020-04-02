<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        $start = new \DateTime("2020-01-30");
        $end = new \DateTime("2020-01-31");

        $import = new ImportSalesToDB($start,$end);

        return $import->getImported();
    }

}
