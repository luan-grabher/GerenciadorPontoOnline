<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArrayError extends Model
{
    public static function get(string $functionName, string $message){
        return [
            'error' => "[" . $functionName . "] " .  $message
        ];
    }
}
