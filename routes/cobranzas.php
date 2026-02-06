<?php
use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;

ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    $options = [
        'as' => 'api'
    ];

    // Clientes Cobranzas
    ApiRoute::post('cobranzas/clientes/import', ['as' => 'api.cobranzas.clientes.import', 'uses' => 'CobranzasClientesController@import']);
    ApiRoute::resource('cobranzas-clientes', 'CobranzasClientesController', $options);

    // Bases Clientes Cobranzas
    ApiRoute::resource('cobranzas-bases-clientes', 'CobranzasBasesClientesController', $options);

    // Carteras Cobranzas
    ApiRoute::post('cobranzas/carteras/import', ['as' => 'api.cobranzas.carteras.import', 'uses' => 'CobranzasCarterasController@import']);
    ApiRoute::resource('cobranzas-carteras', 'CobranzasCarterasController', $options);

    // Bases Carteras Cobranzas
    ApiRoute::resource('cobranzas-bases-carteras', 'CobranzasBasesCarterasController', $options);

    // Pagos Cobranzas
    ApiRoute::post('cobranzas/pagos/import', ['as' => 'api.cobranzas.pagos.import', 'uses' => 'CobranzasPagosController@import']);
    ApiRoute::resource('cobranzas-pagos', 'CobranzasPagosController', $options);

    // Bases Pagos
    ApiRoute::resource('cobranzas-bases-pagos', 'CobranzasBasesPagosController', $options);

});