<?php

namespace App\Http\Controllers;

use App\Jobs\ImportPagarmeBalanceoperations;
use App\pagarmeRecebimento;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class PagarmeController extends Controller
{
    public function jsonBalanceOperations(Request $request){
        try {

            $oneDay = 86400;

            $inicio = strtotime($request->inicio)*1000;
            $fim = (strtotime($request->fim)+$oneDay)*1000;

            return pagarmeRecebimento::getJsonFromAPI($inicio,$fim);
        }catch(\Exception $e){
            return "";
        }
    }

    public function pageImportIndex(){
        return view('import.pagarme.index');
    }
}
