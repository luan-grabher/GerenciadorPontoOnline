<?php

namespace App\Http\Controllers;

use App\Http\Requests\JsonRequest;
use App\Http\Requests\RangeDateRequest;
use App\Jobs\ImportPagarmeBalanceoperations;
use App\pagarmeRecebimento;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Artisan;
use Psy\Util\Json;
use Symfony\Component\Process\Process;

class PagarmeController extends Controller
{
    public function jsonBalanceOperations(JsonRequest $request){
        try {
            $datas = $request->getStartEnd();

            return pagarmeRecebimento::getJsonFromAPI($datas['start'],$datas['end']);
        }catch(\Exception $e){
            return "";
        }
    }

    public function pageImportRecebimentos(){
        return view('import.pagarme.recebimentos');
    }

    public function pageImportRecebimentosStartImport(RangeDateRequest $request){
        $dates = $request->getStartEnd();


        return view(
            'import.pagarme.recebimentos',
            [
                'messages'=> pagarmeRecebimento::importDataFromAPIToDatabase($dates['start'],$dates['end'])
            ]
        );
    }
}
