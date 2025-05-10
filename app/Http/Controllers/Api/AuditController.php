<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Audit\IndexRequest;
use App\Http\Requests\Api\Audit\StoreRequest;
use App\Http\Requests\Api\Audit\UpdateRequest;
use App\Http\Requests\Api\Audit\DeleteRequest;
use App\Models\Audit;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditExport;


class AuditController extends ApiBaseController
{
    protected $model = Audit::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();
        $userId = $this->getIdFromHash($request->user_id);

        if ($request->has('user_id') && $request->user_id != '') {
            $query = $query->where('audits.user_id', $userId );
        }
        if ($request->has('event') && $request->event != '') {
            $query = $query->where('audits.event', $request->event);
        }
        if ($request->has('auditable_type') && $request->auditable_type != '') {
            $query = $query->where('audits.auditable_type', $request->auditable_type);
        }
        return $query;
    }

    public function auditData()
    {
        $auditData = Audit::groupBy('auditable_type')->select('auditable_type')->get();
        return ApiResponse::make('Success', ['data' => $auditData]);
    }

    public function auditDownload()
    {
        $request = request();
        config([
            'api.cors' => false,
        ]);
        $audits =  $this->index();
        $responseData = json_decode($audits->getContent(), true);

        $resultArray = $responseData['data'];

        if ($resultArray) {
            return (new AuditExport($resultArray))->download('audit' . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
    }
  
}

  
