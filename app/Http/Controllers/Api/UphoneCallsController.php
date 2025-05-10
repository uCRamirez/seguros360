<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\UphoneCalls\IndexRequest;
use App\Models\UphoneCalls;
use App\Models\Lead;
use Examyou\RestAPI\ApiResponse;

class UphoneCallsController extends ApiBaseController
{
    protected $model = UphoneCalls::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();
        if ($request->has('lead_id') && $request->lead_id != "") {
            $leadId = $this->getIdFromHash($request->lead_id);
            $query = $query->where('lead_id', $leadId);
        }

        return $query;
    }

    public function saveUphoneCalls()
    {
        $request = request();
        $success = false;
        $user = user();

        if ($request->has('guid')) {
            $guid = $request->guid;

            if ($guid != '') {
                $calls = UphoneCalls::where('user_id', $user->id)->where('guid', $guid)->first();
                if (!$calls) {
                    $calls = new UphoneCalls();
                }
            } else {
                $calls = new UphoneCalls();
            }


            if ($request->has('lead_id') && $request->lead_id != '') {
                $leadId = $this->getIdFromHash($request->lead_id);
                $lead = Lead::find($leadId);
                if ($lead) {
                    $calls->campaign_id = $lead->campaign_id;
                    $calls->lead_id = $lead->id;
                }
            }
            $calls->company_id = company()->id;
            $calls->user_id = $user->id;
            $calls->campaign = $request->has('campaign') ? $request->campaign : '';
            $calls->client_id = $request->has('clientId') ? $request->clientId : '';
            $calls->date = $request->has('date') ? $request->date : '';
            $calls->direction = $request->has('direction') ? $request->direction : '';
            $calls->duration = $request->has('duration') ? $request->duration : '';
            $calls->guid = $request->has('guid') ? $request->guid : '';
            $calls->discriptions = $request->has('needsDispositions') && $request->has('dispositions') ?  $request->dispositions : '';
            $calls->number = $request->has('number') ? $request->number : '';
            $calls->response_data = $request->all();
            $calls->save();



            $success = true;
        }

        return ApiResponse::make('Success', [
            'success' => $success,
        ]);
    }
}
