<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasPagos;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasPagos\IndexRequest;
use App\Http\Requests\Api\CobranzasPagos\StoreRequest;
use App\Http\Requests\Api\CobranzasPagos\UpdateRequest;
use App\Http\Requests\Api\CobranzasPagos\DeleteRequest;

class CobranzasPagosController extends ApiBaseController
{
    protected $model = CobranzasPagos::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

}
