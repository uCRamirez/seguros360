<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasCarteras;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasCarteras\IndexRequest;
use App\Http\Requests\Api\CobranzasCarteras\StoreRequest;
use App\Http\Requests\Api\CobranzasCarteras\UpdateRequest;
use App\Http\Requests\Api\CobranzasCarteras\DeleteRequest;
use App\Http\Requests\Api\CobranzasClientes\ImportRequest;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Imports\CobCarterasImport;
use Maatwebsite\Excel\Facades\Excel;

class CobranzasCarterasController extends ApiBaseController
{
    protected $model = CobranzasCarteras::class;

    protected $indexRequest = IndexRequest::class;
    protected $importRequest = ImportRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function import(ImportRequest $request){
        if ($request->hasFile('file')) {
            try {
                $nombreBase = $request->input('nombre_base') ?? pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
                Excel::import(new CobCarterasImport($nombreBase), request()->file('file'));
                return ApiResponse::make('success');
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage());
            }
        }else {
            throw new ApiException('Invalid file');
        }
    }

}
