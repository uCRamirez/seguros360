<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasClientes;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasClientes\IndexRequest;
use App\Http\Requests\Api\CobranzasClientes\ImportRequest;
use App\Http\Requests\Api\CobranzasClientes\StoreRequest;
use App\Http\Requests\Api\CobranzasClientes\UpdateRequest;
use App\Http\Requests\Api\CobranzasClientes\DeleteRequest;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Imports\CobClientesImport;
use Maatwebsite\Excel\Facades\Excel;

class CobranzasClientesController extends ApiBaseController
{
    protected $model = CobranzasClientes::class;

    protected $indexRequest = IndexRequest::class;
    protected $importRequest = ImportRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function import(ImportRequest $request){
        if ($request->hasFile('file')) {
            try {
                $nombreBase = $request->input('nombre_base') ?? pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
                Excel::import(new CobClientesImport($nombreBase), request()->file('file'));
                return ApiResponse::make('success');
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage());
            }
        }else {
            throw new ApiException('Invalid file');
        }
    }
}