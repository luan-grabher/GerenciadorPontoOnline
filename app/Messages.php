<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    private array $messages = [];

    public function add(string $message,string $type = 'info'){
        $this->messages[] = ['type' =>$type,'text' => $message];
    }

    public function getArray(){
        return $this->messages;
    }
}
