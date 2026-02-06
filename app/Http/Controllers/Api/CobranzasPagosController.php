<?php

namespace App\Http\Controllers\Api;

use App\Models\CobranzasPagos;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\CobranzasPagos\IndexRequest;
use App\Http\Requests\Api\CobranzasPagos\StoreRequest;
use App\Http\Requests\Api\CobranzasPagos\ImportRequest;
use App\Http\Requests\Api\CobranzasPagos\UpdateRequest;
use App\Http\Requests\Api\CobranzasPagos\DeleteRequest;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Imports\CobPagosImport;
use Maatwebsite\Excel\Facades\Excel;

class CobranzasPagosController extends ApiBaseController
{
    protected $model = CobranzasPagos::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $importRequest = ImportRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function import(ImportRequest $request){
        if ($request->hasFile('file')) {
            try {
                $nombreBase = $request->input('nombre_base') ?? pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
                Excel::import(new CobPagosImport($nombreBase), request()->file('file'));
                return ApiResponse::make('success');
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage());
            }
        }else {
            throw new ApiException('Invalid file');
        }
    }

}
