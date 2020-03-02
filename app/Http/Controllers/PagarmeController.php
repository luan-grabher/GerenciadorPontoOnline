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

            $inicio = strtotime($request->inicio);
            $fim = strtotime($request->fim);

            return pagarmeRecebimento::getJsonFromAPI($inicio,$fim);
        }catch(\Exception $e){
            return "";
        }
    }

    public function pageImportIndex(){
        return view('import.pagarme.index');
    }
}
