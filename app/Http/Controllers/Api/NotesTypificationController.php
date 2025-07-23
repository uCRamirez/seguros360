<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\NotesTypification\DeleteRequest;
use App\Http\Requests\Api\NotesTypification\ImportRequest;
use App\Http\Requests\Api\NotesTypification\IndexRequest;
use App\Http\Requests\Api\NotesTypification\StoreRequest;
use App\Http\Requests\Api\NotesTypification\StoreMultipleRequest;
use App\Http\Requests\Api\NotesTypification\UpdateRequest;
use App\Imports\NotesTypificationImport;
use App\Models\NotesTypification;
use App\Models\Product;
use App\Models\LeadLog;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Maatwebsite\Excel\Facades\Excel;

class NotesTypificationController extends ApiBaseController
{
    protected $model = NotesTypification::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $storeMultipleRequest = StoreMultipleRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();

        if ($request->has('name') && $request->name != "") {
            $searchString = $request->name;
            $query = $query->where('notes_typifications.name', 'like', '%' . $searchString . '%');
        }

        return $query;
    }

    public function destroying(NotesTypification $notesTypification)
    {
        // Can not delete parent category
        // $childCategoryCount = NotesTypification::where('parent_id', $notesTypification->id)->count();
        // if ($childCategoryCount > 0) {
        //     throw new ApiException('Parent category cannot be deleted. Please delete child category first.');
        // }

        // Category assigned to any product will not be deleted
        // $productCount = Product::where('category_id', $notesTypification->id)->count();
        // if ($productCount > 0) {
        //     throw new ApiException('Cateogry assigned to any product is not deletable.');
        // }

        // \Log::info('notesTypification', [$notesTypification]);

        return $notesTypification;
    }

    public function addMultipleTypification(StoreMultipleRequest $request)
    {

        // $request->validate();
        // $request = request();
        // \Log::info('campaign', [$request->campaign_id]);
        // \Log::info('notes', [$request->notes]);

        foreach ($request->notes as $typfication) {
            // \Log::info('typfication', [$typfication]);
            if ($typfication['typification_1']) {
                $typification1 = new NotesTypification();
                $typification1->campaign_id = $request->campaign_id;
                $typification1->name = $typfication['typification_1'];

                if(!$typfication['typification_2']){
                    $typification1->sale = $typfication['sale'] ?? false;
                    $typification1->schedule = $typfication['schedule'] ?? false;
                }

                $typification1->save();
            }

            if ($typfication['typification_1'] || $typfication['typification_2']) {
                $typification2 = new NotesTypification();
                $typification2->name = $typfication['typification_2'];
                $typification2->parent_id = $typification1->id;

                if(!$typfication['typification_3']){
                    $typification2->sale = $typfication['sale'] ?? false;
                    $typification2->schedule = $typfication['schedule'] ?? false;
                }

                $typification2->name ? $typification2->save() : '';
            }

            if ($typfication['typification_1'] && $typfication['typification_2'] && $typfication['typification_3']) {
                $typification3 = new NotesTypification();
                $typification3->name = $typfication['typification_3'];
                $typification3->parent_id = $typification2->id;

                if(!$typfication['typification_4']){
                    $typification3->sale = $typfication['sale'] ?? false;
                    $typification3->schedule = $typfication['schedule'] ?? false;
                }

                $typification3->name? $typification3->save() : '';
            }

            if ($typfication['typification_1'] && $typfication['typification_2'] && $typfication['typification_3'] && $typfication['typification_4']) {
                $typification4 = new NotesTypification();
                $typification4->name = $typfication['typification_4'];
                $typification4->parent_id = $typification3->id;
                $typification4->sale = $typfication['sale'] ?? false;
                $typification4->schedule = $typfication['schedule'] ?? false;
                $typification4->name ? $typification4->save() : '';
            }
        }

        return ApiResponse::make('Success', []);
    }

    public function import2(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new NotesTypificationImport, request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    public function import(ImportRequest $request)
    {
        try {
            if ($request->hasFile('file')) {
                Excel::import(new NotesTypificationImport, request()->file('file'));
            }

            // \Log::info('NotesTypificationImport ejecutado correctamente');
            return ApiResponse::make('Imported Successfully', []);

        } catch (ApiException $e) {
            \Log::error('Error en NotesTypificationImport', [
                'mensaje'   => $e->getMessage(),
            ]);

            // return ApiResponse::error($e->getMessage(), 422);
            return ApiResponse::make(
                $e->getMessage(),      
                [],                   
                true,
            );


        } catch (\Throwable $e) {
            \Log::error('Ocurrió un error inesperado', [
                'mensaje'   => $e->getMessage(),
            ]);
            
            return ApiResponse::make('Ocurrió un error inesperado',[$e]);
        }
    }

}