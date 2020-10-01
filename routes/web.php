<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(url('login'));
});

Auth::routes();
Route::get('/register', function () {
    return redirect(url('login'));
});

if (__('home') != 'home') {
    Route::get('/home', function () {
        return redirect(route('home'));
    });
}
Route::get('/' . __('home'), 'HomeController@index')->name('home');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

if (env('APP_DEBUG') == 'true') {
    Route::group(['prefix' => 'test'], function () {
        Route::get('', 'TestController@test');
    });
}

Route::group(['prefix' => __('import')], function () {
    Route::group(['prefix' => 'pagarme'], function(){
        Route::get('recebimentos',  function(){
            return view('layouts.import',['title'=>"Importar Pagarme Recebimentos",'button_name' => "Importar"]);
        })->name('import.pagarme.recebimentos');
        Route::post('recebimentos', 'PagarmeController@importRecebimentosFromAPI')->name('import.pagarme.recebimentos.post');
        Route::get('vendas', function(){
            return view(
                'layouts.searchWithRange',
                [
                    'title'=>'Importar Vendas Pagarme',
                    'button_name' => "Importar"
                ]
            );
        })->name('import.pagarme.vendas');
        Route::post('vendas', 'PagarmeController@importVendasFromAPI')->name('import.pagarme.vendas.post');
    });
    Route::group(['prefix' => 'erp'], function(){
        Route::get('vendas', function(){
            return view('layouts.import',['title'=>"Importar ERP Vendas",'button_name' => "Importar"]);
        })->name('import.erp.vendas');
        Route::post('vendas', 'ErpController@importERPSalesInRangeDate')->name('import.erp.vendas.post');
    });
});

Route::group(['prefix' => __('consult')], function () {
    Route::group(['prefix' => 'pagarme'], function(){
        Route::get('recebimentos', function (){
            return view('layouts.consult',['title'=>"Pagarme Consultar Recebimentos"]);
        })->name('consult.pagarme.recebimentos');
        Route::post('recebimentos', 'PagarmeController@viewGetRecebimentos')->name('consult.pagarme.recebimentos.post');
        Route::get('vendas', function(){
            return view(
                'layouts.searchWithRange',
                [
                    'title'=>'Consultar Vendas Pagarme'
                ]
            );
        })->name('consult.pagarme.vendas');
        Route::post('vendas', 'PagarmeController@viewGetVendas')->name('consult.pagarme.vendas.post');
    });
    Route::group(['prefix' => 'erp'], function(){
        Route::get('vendas', function(){
            return view('layouts.consult',['title'=>"ERP Consultar Vendas"]);
        })->name('consult.erp.vendas');
        Route::post('vendas', 'ErpController@viewGetVendas')->name('consult.erp.vendas.post');
    });
});

Route::group(['prefix'=>'pagarme'],function(){
   Route::group(['prefix'=>'reports'],function(){
       Route::group(['prefix'=>'receivable'],function(){
            Route::get('',function(){return view('layouts.searchWithRange',['title'=>'Pagarme A Receber']);})->name('pagarme.reports.receivable');
            Route::post('','PagarmeController@selectReportReceivables');
       });
   });
});

Route::group(['prefix' => __('analysis')], function () {
    Route::group(['prefix' => 'balances'], function(){
        Route::group(['prefix'=>'tid'], function(){
            Route::get('', function(){return view('layouts.searchWithRange',['title'=>'Saldos TID']);})->name('analysis.balances.tid');
            Route::post('', 'AnalysisController@getTotalTid')->name('analysis.balances.tid.post');
        });
    });
});
