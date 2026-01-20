<?php
use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;

ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    $options = [
        'as' => 'api'
    ];

    // Clientes cobranzas
    ApiRoute::resource('cobranzas-clientes', 'CobranzasClientesController', $options);

    // Carteras cobranzas
    ApiRoute::resource('cobranzas-carteras', 'CobranzasCarterasController', $options);

    // Pagos cobranzas
    ApiRoute::resource('cobranzas-pagos', 'CobranzasPagosController', $options);

});