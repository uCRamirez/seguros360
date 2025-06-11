<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\MotivoCancelacion\IndexRequest;
use App\Http\Requests\Api\MotivoCancelacion\StoreRequest;
use App\Http\Requests\Api\MotivoCancelacion\UpdateRequest;
use App\Http\Requests\Api\MotivoCancelacion\DeleteRequest;
use App\Models\MotivoCancelacion;
use Examyou\RestAPI\Exceptions\ApiException;

class MotivoCancelacionController extends ApiBaseController
{
    protected $model = MotivoCancelacion::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
  
}
