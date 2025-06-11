<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\PlantillaCalidad\IndexRequest;
use App\Http\Requests\Api\PlantillaCalidad\StoreRequest;
use App\Http\Requests\Api\PlantillaCalidad\UpdateRequest;
use App\Http\Requests\Api\PlantillaCalidad\DeleteRequest;
use App\Models\PlantillaCalidad;
use Examyou\RestAPI\Exceptions\ApiException;

class PlantillaCalidadController extends ApiBaseController
{
    protected $model = PlantillaCalidad::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
  
}
