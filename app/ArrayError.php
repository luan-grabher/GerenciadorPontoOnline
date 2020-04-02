<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArrayError extends Model
{
    public static function string(string $functionName, string $message){
        return [
            'error' => "[" . $functionName . "] " .  $message
        ];
    }
    public static function error(string $functionName, \Exception $error){
        return self::string($functionName,$error->getMessage());
    }
}
