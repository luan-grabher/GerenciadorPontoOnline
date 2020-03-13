<?php

namespace App\Http\Controllers;

use App\Analysis;
use App\ErpVenda;
use App\Http\Requests\RangeDateRequest;
use App\Messages;
use App\PagarmeRecebimento;
use App\PagarmeVenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function getTotalTid(RangeDateRequest $request)
    {
        $dates = [
            "start" => $request->input('inicio'),
            "end" => $request->input('fim')
        ];

        $results = Analysis::getTidBalance($dates['start'],$dates['end']);

        $messages = new Messages();
        //$messages->add($results->toSql());

        return view(
            "layouts.consult",
            [
                'title' => "Totais TID",
                "messages"=>$messages,
                "results" => $results
            ]
        );
    }
}
