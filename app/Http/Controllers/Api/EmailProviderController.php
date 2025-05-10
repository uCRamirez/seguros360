<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EmailProvider\IndexRequest;
use App\Http\Requests\Api\EmailProvider\StoreRequest;
use App\Http\Requests\Api\EmailProvider\UpdateRequest;
use App\Http\Requests\Api\EmailProvider\DeleteRequest;
use App\Models\EmailProvider;
use Examyou\RestAPI\ApiResponse;

class EmailProviderController extends ApiBaseController
{
    protected $model = EmailProvider::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    // public function allProviders()
    // {
    //     $allProviders = EmailProvider::select('id', 'name')->get();

    //     return ApiResponse::make('Success', [
    //         'providers' => $allProviders
    //     ]);
    // }
}