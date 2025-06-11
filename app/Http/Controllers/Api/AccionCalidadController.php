<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\AccionCalidad\IndexRequest;
use App\Http\Requests\Api\AccionCalidad\StoreRequest;
use App\Http\Requests\Api\AccionCalidad\UpdateRequest;
use App\Http\Requests\Api\AccionCalidad\DeleteRequest;
use App\Models\AccionCalidad;
use Examyou\RestAPI\Exceptions\ApiException;

class AccionCalidadController extends ApiBaseController
{
    protected $model = AccionCalidad::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
  
}
