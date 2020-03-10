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
        return view('layouts.import',['title'=>"Importar Pagarme Recebimentos",'button_name' => "Importar"]);
    }

    public function pageImportRecebimentosStartImport(RangeDateRequest $request){
        $dates = $request->getStartEnd();

        return view(
            'layouts.import',
            [
                'title'=>"Importar Pagarme Recebimentos",
                'button_name' => "Importar",
                'messages'=> pagarmeRecebimento::importDataFromAPIToDatabase($dates['start'],$dates['end'])
            ]
        );
    }

    public function pageConsultRecebimentos()
    {
        return view('layouts.consult',['title'=>"Pagarme Consultar Recebimentos"]);
    }

    public function pageConsultRecebimentosRequest(RangeDateRequest $request)
    {
        $dates = [
            'start'=>
                date("Y-m-d",strtotime($request->input('inicio'))),
            'end'=>
                date("Y-m-d",strtotime($request->input('fim')))
        ];
        $sales = PagarmeRecebimento::
        whereDate(
            'dataRecebimento', ">=", $dates['start'])->
        whereDate(
            'dataRecebimento', "<", $dates['end']
        )->get();
        return view('layouts.consult',
            ['title'=>'Pagarme Consultar Recebimentos',
                'results' => $sales->toArray(),
                'filters'=>[
                    'Início'=>$dates['start'],
                    'Fim'=>$dates['end'],
                ]
            ]);
    }
}
