<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EvaluacionCalidad\IndexRequest;
use App\Http\Requests\Api\EvaluacionCalidad\StoreRequest;
use App\Http\Requests\Api\EvaluacionCalidad\UpdateRequest;
use App\Http\Requests\Api\EvaluacionCalidad\DeleteRequest;
use App\Models\EvaluacionCalidad;
use Examyou\RestAPI\Exceptions\ApiException;

class EvaluacionCalidadController extends ApiBaseController
{
    protected $model = EvaluacionCalidad::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
  
}
