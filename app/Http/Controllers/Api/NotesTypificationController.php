<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\NotesTypification\IndexRequest;
use App\Http\Requests\Api\NotesTypification\StoreRequest;
use App\Http\Requests\Api\NotesTypification\UpdateRequest;
use App\Http\Requests\Api\NotesTypification\DeleteRequest;
use App\Http\Requests\Api\NotesTypification\ImportRequest;

use App\Models\NotesTypification;
use App\Models\Product;
use App\Models\LeadLog;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NotesTypificationImport;

class NotesTypificationController extends ApiBaseController
{
    protected $model = NotesTypification::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
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
        $childCategoryCount = NotesTypification::where('parent_id', $notesTypification->id)->count();
        if ($childCategoryCount > 0) {
            throw new ApiException('Parent category cannot be deleted. Please delete child category first.');
        }

        // Category assigned to any product will not be deleted
        $productCount = Product::where('category_id', $notesTypification->id)->count();
        if ($productCount > 0) {
            throw new ApiException('Cateogry assigned to any product is not deletable.');
        }

        return $notesTypification;
    }

    public function addMultipleTypification()
    {

        $request = request();

        foreach ($request->notes as $typfication) {

            if ($typfication['typification_1']) {
                $typification1 = new NotesTypification();
                $typification1->name = $typfication['typification_1'];
                $typification1->save();
            }

            if ($typfication['typification_1'] || $typfication['typification_2']) {
                $typification2 = new NotesTypification();
                $typification2->name = $typfication['typification_2'];
                $typification2->parent_id = $typification1->id;
                $typification2->save();
            }

            if ($typfication['typification_1'] && $typfication['typification_2'] && $typfication['typification_3']) {
                $typification3 = new NotesTypification();
                $typification3->name = $typfication['typification_3'];
                $typification3->parent_id = $typification2->id;
                $typification3->save();
            }

            if ($typfication['typification_1'] && $typfication['typification_2'] && $typfication['typification_3'] && $typfication['typification_4']) {
                $typification4 = new NotesTypification();
                $typification4->name = $typfication['typification_4'];
                $typification4->parent_id = $typification3->id;
                $typification4->save();
            }
        }

        return ApiResponse::make('Success', []);
    }

    public function import(ImportRequest $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new NotesTypificationImport, request()->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }
}