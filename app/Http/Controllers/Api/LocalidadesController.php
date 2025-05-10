<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Localidades\IndexRequest;
use App\Http\Requests\Api\Localidades\StoreRequest;
use App\Http\Requests\Api\Localidades\UpdateRequest;
use App\Http\Requests\Api\Localidades\DeleteRequest;
use App\Models\Localidades;

class LocalidadesController extends ApiBaseController
{
    protected $model = Localidades::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

  
}
