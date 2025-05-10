<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\MessageTemplate\IndexRequest;
use App\Http\Requests\Api\MessageTemplate\StoreRequest;
use App\Http\Requests\Api\MessageTemplate\UpdateRequest;
use App\Http\Requests\Api\MessageTemplate\DeleteRequest;
use App\Http\Requests\Api\MessageTemplate\UpdateStatusRequest;
use App\Models\MessageTemplate;
use Examyou\RestAPI\ApiResponse;

class MessageTemplateController extends ApiBaseController
{
    protected $model = MessageTemplate::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();

        if (!$user->ability('admin', 'message_templates_view_all')) {
            $query = $query->where('created_by', $user->id);
        }

        return $query;
    }

    public function storing($emailTemplate)
    {
        $loggedUser = user();

        $emailTemplate->created_by = $loggedUser->id;
        $emailTemplate->updated_by = $loggedUser->id;

        return $emailTemplate;
    }

    public function updating($emailTemplate)
    {
        $loggedUser = user();

        $emailTemplate->updated_by = $loggedUser->id;

        return $emailTemplate;
    }

    public function updateStatus(UpdateStatusRequest $request)
    {
        $xid = $request->xid;
        $id = $this->getIdFromHash($xid);

        $emailTemplate = MessageTemplate::find($id);
        $emailTemplate->status = $request->status;
        $emailTemplate->save();

        return ApiResponse::make('Success', []);
    }

    public function allMessageTemplates()
    {
        $user = user();

        $messageTemplates = MessageTemplate::select('id', 'name', 'message','message_provider_id','code')
            ->where('status', 1)
            // ->where(function ($query) use ($user) {
            //     $query->where('created_by', $user->id)
            //         ->orWhere('sharable', 1);
            // })
            ->get();

        return ApiResponse::make('Success', [
            'message_templates' => $messageTemplates
        ]);
    }
}