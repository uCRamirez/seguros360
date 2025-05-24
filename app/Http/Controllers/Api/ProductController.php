<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Product\IndexRequest;
use App\Http\Requests\Api\Product\StoreRequest;
use App\Http\Requests\Api\Product\UpdateRequest;
use App\Http\Requests\Api\Product\DeleteRequest;
use App\Http\Requests\Api\Product\ImportRequest;
use App\Models\Product;
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
            Excel::import(new ProductImport, request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    protected function modifyIndex($query)
    {
        $user    = user();
        $request = request();

        // Si no tiene permiso o pide 'self' (o no viene view_type), restringir
        if (
            ! $user->ability('admin', 'campaigns_view_all')
            || ! $request->has('view_type')
            || $request->get('view_type') === 'self'
        ) {
            $query->whereIn('campaign_id', function($q) use ($user) {
                $q->select('campaign_id')
                ->from('campaign_users')
                ->where('user_id', $user->id);
            });
        }

        return $query;
    }


    
}
