<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasBasesCarteras;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasBasesCarteras\IndexRequest;
use App\Http\Requests\Api\CobranzasBasesCarteras\StoreRequest;
use App\Http\Requests\Api\CobranzasBasesCarteras\UpdateRequest;
use App\Http\Requests\Api\CobranzasBasesCarteras\DeleteRequest;

class CobranzasBasesCarterasController extends ApiBaseController
{
    protected $model = CobranzasBasesCarteras::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}
