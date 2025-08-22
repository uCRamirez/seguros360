<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\UphoneCalls\IndexRequest;
use App\Http\Requests\Api\UphoneCalls\UpdateRequest;
use App\Models\UphoneCalls;
use App\Models\Lead;
use Examyou\RestAPI\ApiResponse;
use Carbon\Carbon;

class UphoneCallsController extends ApiBaseController
{
    protected $model = UphoneCalls::class;

    protected $indexRequest = IndexRequest::class;
    protected $updateRequest = UpdateRequest::class;

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
        // \Log::info('request', [$request->all()]);
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

            if ($request->has('lead_id')) {
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

    public function updateUphoneCalls()
    {
        $request = request();
        $user = user();
        // \Log::info('uphone.update.request', [$request->all()]);

        $phone      = $request->input('phone');
        $leadHash   = $request->input('lead_id');     
        $campaignId = $request->input('campaign_id');

        $startOfDay = Carbon::today()->startOfDay(); // 00:00:00 de hoy
        $endOfDay   = Carbon::today()->endOfDay();   // 23:59:59 de hoy

        $call = UphoneCalls::where('user_id', $user->id)
            ->where('number', $phone)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (!$call) {
            return ApiResponse::make('Not Found', ['success' => false], 404);
        }

        // Solo actualizar si NO tiene lead_id asignado aún
        if (empty($call->lead_id) || $call->lead_id === 0 || $call->lead_id === '0') {
            $call->lead_id = $this->getIdFromHash($leadHash) ?? null;
            $call->campaign_id = $campaignId ?? null;
            $call->save();

            return ApiResponse::make('Success', [
                'success' => true,
                'updated' => true,
            ]);
        }

        // Ya tenía lead_id: no tocar
        return ApiResponse::make('Already Assigned', [
            'success' => true,
            'updated' => false,
        ]);
    }


}
