<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasCarteras;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasCarteras\IndexRequest;
use App\Http\Requests\Api\CobranzasCarteras\StoreRequest;
use App\Http\Requests\Api\CobranzasCarteras\UpdateRequest;
use App\Http\Requests\Api\CobranzasCarteras\DeleteRequest;

class CobranzasCarterasController extends ApiBaseController
{
    protected $model = CobranzasCarteras::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

}
