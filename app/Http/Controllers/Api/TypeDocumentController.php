<?php

namespace App\Http\Controllers\Api;

use App\Models\TypeDocument;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\TypeDocument\IndexRequest;
use App\Http\Requests\Api\TypeDocument\StoreRequest;
use App\Http\Requests\Api\TypeDocument\UpdateRequest;
use App\Http\Requests\Api\TypeDocument\DeleteRequest;

class TypeDocumentController extends ApiBaseController
{
    protected $model = TypeDocument::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

}
