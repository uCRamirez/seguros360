<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EstadoCalidadVenta\IndexRequest;
use App\Http\Requests\Api\EstadoCalidadVenta\StoreRequest;
use App\Http\Requests\Api\EstadoCalidadVenta\UpdateRequest;
use App\Http\Requests\Api\EstadoCalidadVenta\DeleteRequest;
use App\Models\EstadoCalidadVenta;
use Examyou\RestAPI\Exceptions\ApiException;

class EstadoCalidadVentaController extends ApiBaseController
{
    protected $model = EstadoCalidadVenta::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
  
}
