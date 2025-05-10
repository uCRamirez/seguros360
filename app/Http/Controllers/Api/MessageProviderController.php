<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\MessageProvider\IndexRequest;
use App\Http\Requests\Api\MessageProvider\StoreRequest;
use App\Http\Requests\Api\MessageProvider\UpdateRequest;
use App\Http\Requests\Api\MessageProvider\DeleteRequest;
use App\Models\MessageProvider;
use Examyou\RestAPI\ApiResponse;

class MessageProviderController extends ApiBaseController
{
    protected $model = MessageProvider::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function allProviders()
    {
        $allProviders = MessageProvider::select('id', 'name')->get();

        return ApiResponse::make('Success', [
            'providers' => $allProviders
        ]);
    }
}
