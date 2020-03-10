<?php

namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public static function test(){
        $date = new \DateTime();
        $date->setTimestamp(1577836800);
        return "Isso é uma data: " . $date->format("Y-m-d");

    }
}
