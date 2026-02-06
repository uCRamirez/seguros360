<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasBasesClientes;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasBasesClientes\IndexRequest;
use App\Http\Requests\Api\CobranzasBasesClientes\StoreRequest;
use App\Http\Requests\Api\CobranzasBasesClientes\UpdateRequest;
use App\Http\Requests\Api\CobranzasBasesClientes\DeleteRequest;

class CobranzasBasesClientesController extends ApiBaseController
{
    protected $model = CobranzasBasesClientes::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}
