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

Route::group(['prefix'=>'json', 'middleware'=>'auth'], function (){
    Route::group(['prefix'=>'pagarme'], function(){
        Route::get("recebimentos/{inicio}/{fim}","PagarmeController@jsonBalanceOperations")->name('json.pagarme.recebimentos');
    });
    Route::group(['prefix'=>'erp'],function(){
        Route::get("vendas/{inicio}/{fim}","ErpController@jsonVendas")->name('json.erp.vendas');
    });
});

Route::group(['prefix' => __('import')], function () {
    Route::group(['prefix' => 'pagarme'], function(){
        Route::get('recebimentos', 'PagarmeController@pageImportRecebimentos')->name('import.pagarme.recebimentos');
        Route::post('recebimentos', 'PagarmeController@pageImportRecebimentosStartImport')->name('import.pagarme.recebimentosStartImport');
    });
});
