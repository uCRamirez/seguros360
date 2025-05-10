<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeadStatus\IndexRequest;
use App\Http\Requests\Api\LeadStatus\StoreRequest;
use App\Http\Requests\Api\LeadStatus\UpdateRequest;
use App\Http\Requests\Api\LeadStatus\DeleteRequest;
use App\Models\LeadStatus;

class LeadStatusController extends ApiBaseController
{
    protected $model = LeadStatus::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

  
}
