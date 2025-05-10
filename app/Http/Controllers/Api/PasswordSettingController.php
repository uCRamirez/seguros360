<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\PasswordSetting\IndexRequest;  
use App\Http\Requests\Api\PasswordSetting\StoreRequest;
use App\Models\PasswordSetting;
use Examyou\RestAPI\Exceptions\ApiException;

class PasswordSettingController extends ApiBaseController
{
    protected $model = PasswordSetting::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;

}
