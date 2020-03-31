<?php

namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        return
            [
                'date'=> date('d-m-Y')
            ]
        ;
    }

}
