<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;   
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\ProductosVenta\IndexRequest;
use App\Http\Requests\Api\ProductosVenta\StoreRequest;
use App\Http\Requests\Api\ProductosVenta\UpdateRequest;
use App\Http\Requests\Api\ProductosVenta\DeleteRequest;
use App\Models\Venta;
use App\Models\ProductosVenta;
use App\Models\LeadLog;
use App\Models\Lead;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;


class ProductosVentaController extends ApiBaseController
{
    protected $model = ProductosVenta::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

}
