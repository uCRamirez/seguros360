<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasClientes;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasClientes\IndexRequest;
use App\Http\Requests\Api\CobranzasClientes\StoreRequest;
use App\Http\Requests\Api\CobranzasClientes\UpdateRequest;
use App\Http\Requests\Api\CobranzasClientes\DeleteRequest;

class CobranzasClientesController extends ApiBaseController
{
    protected $model = CobranzasClientes::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}