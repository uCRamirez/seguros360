<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeadLog\IndexRequest;
use App\Http\Requests\Api\LeadLog\StoreRequest;
use App\Http\Requests\Api\LeadLog\UpdateRequest;
use App\Http\Requests\Api\LeadLog\DeleteRequest;
use App\Models\LeadLog;
use App\Models\Lead;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\CampaignUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LeadLogController extends ApiBaseController
{
    protected $model = LeadLog::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        // \Log::info('modifyIndex SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        $query->select('lead_logs.*');

        $user = user();
        $request = request();

        $usersIds = CampaignUser::where('user_id', $user->id)
            ->pluck('user_id');

        $query = $query->join('leads', 'leads.id', '=', 'lead_logs.lead_id')
            ->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');

        if (!$user->ability('admin', 'leads_view_all')) {
            if ($request->has('log_type') && $request->log_type == 'salesman_bookings') {
                $query = $query->where('lead_logs.created_by_id', $user->id);
                // \Log::info('modifyIndex', ['data' => 1]);
            }
            else if ($request->has('log_type') && $request->log_type == 'message') {
                // \Log::info('modifyIndex', ['data' => 2]);
                $query = $query->where('lead_logs.log_type', 'message');
            }
            else if (
                $request->has('log_type') && $request->has('page_name') &&
                $request->page_name == 'lead_action' &&
                $request->has('lead_id') && $request->lead_id != "" &&
                ($request->log_type == 'call_log' || $request->log_type == 'notes' || $request->log_type == 'all')
            ) {
                // \Log::info('modifyIndex', ['data' => 3]);
            } else {
                // \Log::info('modifyIndex', ['data' => 4]);
                $query = $query->where('lead_logs.user_id', $user->id);
                // \Log::info(['$user->id' => $user->id]);

            }
        }
        else{
            if ($user->ability($user->role->name, 'leads_view_all')) {
                // \Log::info('modifyIndex', ['data' => 5]);
                $campaignIds = CampaignUser::where('user_id', $user->id)
                                    ->pluck('campaign_id');
                $teammateIds = CampaignUser::whereIn('campaign_id', $campaignIds)
                                    ->distinct()
                                    ->pluck('user_id');
                $allUserIds = $teammateIds->push($user->id)->unique();
                $query = $query->whereIn('lead_logs.user_id', $allUserIds);
            }
        }

        if ($request->has('log_type') && $request->log_type == 'lead_follow_up') {
            // \Log::info('modifyIndex', ['data' => 6]);
            $query = $query->where('booking_status', '!=', 'actioned');
        }

        if ($user->ability('admin', 'view_completed_campaigns')) {
            // \Log::info('modifyIndex', ['data' => 7]);
            if ($request->has('campaign_status') && $request->campaign_status == "completed") {
                // \Log::info('modifyIndex', ['data' => 8]);
                $query = $query->where('campaigns.status', '=', 'completed');
            } else {
                // \Log::info('modifyIndex', ['data' => 9]);
                $query = $query->where('campaigns.status', '!=', 'completed');
            }
        } else {
            // \Log::info('modifyIndex', ['data' => 10]);
            $query = $query->where('campaigns.status', '!=', 'completed');
        }

        if ($request->has('lead_id') && $request->lead_id != "") {
            // \Log::info('modifyIndex', ['data' => 11]);
            $leadId = $this->getIdFromHash($request->lead_id);
            $query = $query->where('lead_logs.lead_id', $leadId);
        }

        if ($request->has('campaign_id') && $request->campaign_id != "") {
            // \Log::info('modifyIndex', ['data' => 12]);
            $campaignId = $this->getIdFromHash($request->campaign_id);
            $query = $query->where('leads.campaign_id', $campaignId);
        }

        if ($request->has('log_type') && $request->log_type == 'call_log') {
            // \Log::info('modifyIndex', ['data' => 13]);
            $query = $query->where('lead_logs.time_taken', '>', 0);
        }

        if ($request->has('dates') && $request->dates != "") {
            // \Log::info('modifyIndex', ['data' => 14]);
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('lead_logs.date_time >= ?', [$startDate])
                ->whereRaw('lead_logs.date_time <= ?', [$endDate]);
        }
        
        $filtersParam = $request->query('filters', '');
        // Detectamos si se usan filtros relacionados a ventas
        $needsJoinSale = Str::contains($filtersParam, 'isSale.estadoVenta') ||
                        Str::contains($filtersParam, 'isSale.calidad') ||
                        Str::contains($filtersParam, 'isSale.user_id') ||
                        Str::contains($filtersParam, 'isSale.idVenta') ||
                        Str::contains($filtersParam, 'isSale_calidad.estado');

        // Solo hacemos JOIN a ventas si alguno lo necesita
        if ($needsJoinSale) {
            $query->leftJoin('ventas as isSale', 'isSale.idNota', '=', 'lead_logs.id');
        }

        // Si se requiere el estado más reciente de calidad
        if (Str::contains($filtersParam, 'isSale_calidad.estado')) {
            $query->leftJoin(DB::raw('
                (
                    SELECT ecv.*
                    FROM estados_calidad_venta ecv
                    INNER JOIN (
                        SELECT idVenta, MAX(id) as max_id
                        FROM estados_calidad_venta
                        GROUP BY idVenta
                    ) latest_ecv
                    ON ecv.idVenta = latest_ecv.idVenta AND ecv.id = latest_ecv.max_id
                ) as isSale_calidad
            '), 'isSale_calidad.idVenta', '=', 'isSale.idVenta');
        }

        // \Log::info('modifyIndex SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        return $query;
    }

    public function storing($leadLog)
    {
        $loggedUser = user();
        $request = request();
        // \Log::info('storing', ['data' => $leadLog]);


        if ($request->log_type == '' || $request->log_type != 'notes') {
            throw new ApiException("Not Allowed");
        }

        $leadLog->user_id = $loggedUser->id;
        $leadLog->date_time = Carbon::now();

        return $leadLog;
    }

    public function stored($leadLog)
    {
        // \Log::info('stored', ['data' => $leadLog]);
        $notesCount = LeadLog::where('lead_id', $leadLog->lead_id)
        ->where('log_type', 'notes')
        ->count();
        $lead = Lead::find($leadLog->lead_id);
        $lead->notes_count = $notesCount;
        $lead->save();

    }

    public function updating($leadLog)
    {
        $request = request();
        // \Log::info('updating', ['data' => $leadLog]);
        if ($request->has('user_id') || $request->has('date_time') || $request->log_type == '' || $request->log_type != 'notes') {
            throw new ApiException("Not Allowed");
        }

        return $leadLog;
    }
}