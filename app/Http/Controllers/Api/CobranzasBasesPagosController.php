<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasBasesPagos;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasBasesPagos\IndexRequest;
use App\Http\Requests\Api\CobranzasBasesPagos\StoreRequest;
use App\Http\Requests\Api\CobranzasBasesPagos\UpdateRequest;
use App\Http\Requests\Api\CobranzasBasesPagos\DeleteRequest;

class CobranzasBasesPagosController extends ApiBaseController
{
    protected $model = CobranzasBasesPagos::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}
