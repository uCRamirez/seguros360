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
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () use ($request) {

            foreach ($request->notes as $typification) {

                $parentId = null;                     // padre actual (null = nivel 1)
                $levels   = ['typification_1', 'typification_2', 'typification_3', 'typification_4'];

                foreach ($levels as $idx => $levelKey) {

                    $name = $typification[$levelKey] ?? null;
                    if (!$name) break;                // sin nombre ⇒ no hay más niveles

                    /* ───── ¿YA EXISTE? ───── */
                    $query = NotesTypification::where('name', $name)
                                            ->where('parent_id', $parentId);

                    if ($parentId === null) {        // sólo en nivel 1 se filtra por campaña
                        $query->where('campaign_id', $request->campaign_id);
                    }

                    $node = $query->first();
                    if ($node) {                     // existe ⇒ úsalo como padre y continua
                        $parentId = $node->id;
                        continue;
                    }

                    /* ───── CREAR NUEVO ───── */
                    $node              = new NotesTypification();
                    $node->name        = $name;
                    $node->parent_id   = $parentId;
                    if ($parentId === null) {        // nivel 1
                        $node->campaign_id = $request->campaign_id;
                    }

                    // ¿soy el último nivel que se va a guardar?
                    $isLast = ($idx === count($levels)-1) ||
                            empty($typification[$levels[$idx+1]]);

                    if ($isLast) {
                        $node->sale     = $typification['sale']     ?? false;
                        $node->schedule = $typification['schedule'] ?? false;
                    }

                    // `no_contact` sólo en nivel 3 (idx = 2)
                    if ($idx === 2) {
                        $node->no_contact = $typification['no_contact'] ?? false;
                    }

                    $node->save();
                    $parentId = $node->id;           // nuevo padre para el siguiente nivel
                }
            }
        });

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