<?php

namespace App\Http\Controllers;

use App\ErpVenda;
use App\Http\Requests\RangeDateRequest;
use App\PagarmeRecebimento;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function getTotalTid(RangeDateRequest $request)
    {
        $dates = [
            "start" => $request->input('inicio'),
            "end" => $request->input('fim')
        ];

        $ERPVendas = ErpVenda::
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end']
        )->get();

        $PagarmeRecebimentos = PagarmeRecebimento::
        whereDate(
            'dataPagamento', ">=", $dates['start'])->
        whereDate(
            'dataPagamento', "<", $dates['end']
        )->get();

        return view(
            "layouts.consult",
            [
                'title' => "Totais TID"
            ]
        );
    }
}
