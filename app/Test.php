<?php

namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        $import = new ImportERP(new \DateTime("2020-01-30"),new \DateTime("2020-01-31"));

        return $import->import();
    }

}
