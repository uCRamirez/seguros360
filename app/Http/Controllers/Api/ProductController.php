<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Product\IndexRequest;
use App\Http\Requests\Api\Product\StoreRequest;
use App\Http\Requests\Api\Product\UpdateRequest;
use App\Http\Requests\Api\Product\DeleteRequest;
use App\Http\Requests\Api\Product\ImportRequest;
use App\Models\Product;
use App\Models\CampaignUser;
use App\Imports\ProductImport;
use Examyou\RestAPI\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends ApiBaseController
{
	protected $model = Product::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function import(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            // Excel::import(new ProductImport, request()->file('file'));
            Excel::import(
                new ProductImport(pathinfo(request()->file('file')->getClientOriginalName(), PATHINFO_FILENAME)),
                request()->file('file')
            );
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    protected function modifyIndex($query)
    {
        $user    = user();
        $request = request();
        // \Log::info('modifyIndex', ['$user' => $user]);
        
        if ($user->role->name != 'admin') {
            if(!$user->role->perms->contains('name', 'leads_view_all')){
                // \Log::info('modifyIndex - dentro IF');

                $query->whereIn('campaign_id', function($q) use ($user) {
                    $q->select('campaign_id')
                    ->from('campaign_users')
                    ->where('user_id', $user->id);
                });
            }else{

                // \Log::info('modifyIndex - dentro ELSE');

                $campaignIds = CampaignUser::where('user_id', $user->id)
                ->orderBy('campaign_id')
                ->pluck('campaign_id');

                // \Log::info('modifyIndex - campaignIds', ['$campaignIds' => $campaignIds]);


                $query->whereIn('campaign_id', $campaignIds);

            }
        }

        return $query;
    }


    
}
