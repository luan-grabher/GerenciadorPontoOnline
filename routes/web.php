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
        Route::get('recebimentos', 'PagarmeController@pageImportRecebimentos')->name('import.pagarme.recebimentos');
        Route::post('recebimentos', 'PagarmeController@pageImportRecebimentosStartImport')->name('import.pagarme.recebimentosStartImport');
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
        Route::get('vendas', 'ErpController@pageImportVendas')->name('import.erp.vendas');
        Route::post('vendas', 'ErpController@pageImportVendasStartImport')->name('import.erp.vendasStartImport');
    });
});

Route::group(['prefix' => __('consult')], function () {
    Route::group(['prefix' => 'pagarme'], function(){
        Route::get('recebimentos', 'PagarmeController@pageConsultRecebimentos')->name('consult.pagarme.recebimentos');
        Route::post('recebimentos', 'PagarmeController@pageConsultRecebimentosRequest')->name('consult.pagarme.recebimentosRequest');
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
        Route::get('vendas', 'ErpController@pageConsultVendas')->name('consult.erp.vendas');
        Route::post('vendas', 'ErpController@pageConsultVendasRequest')->name('consult.erp.vendasRequest');
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
