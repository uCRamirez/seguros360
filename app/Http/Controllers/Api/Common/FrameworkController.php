<?php

namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Classes\Common;
use App\Models\Audit;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar DB Facade

class FrameworkController extends ApiBaseController
{
    protected $model = Audit::class;
    
    // To execute a SQL query
    public function executeQuery(Request $request)
    {
        // validate the body request
        $request->validate([
            'query' => 'required|string'
        ]);

        $query = $request->input('query');

        // query's that cannot be executed
        $bloqueadas = ['DROP', 'TRUNCATE', 'ALTER','CREATE']; 
        foreach ($bloqueadas as $palabra) {
            if (stripos($query, $palabra) !== false) {
                return response()->json([
                    "message" => "error",
                    "error" => "Invalid operation"
            ], 403);
            }
        }
        try {
            if (stripos($query, 'CALL') === 0) {
                // If the query is a CALL, execute it with statement
                DB::statement($query);
                return response()->json(["message" => "OK"], 200); // Return OK when the query is a CALL
            } else {
                // Execute other queries normally
                $result = DB::select($query);
                return response()->json([
                    "message" => "OK",
                    "data" => $result
                ], 200); // Return de request infor
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 400);
        }
    }


    // To audit action in auditory
    public function audit(Request $request)
    {
        // Required fields are validated
        $request->validate([
            'action' => 'required|string'
        ]);

        // Get the action
        $action = $request->input('action');

        try {
            // Audit the information
            DB::table('auditory')->insert([
                'dateprocess' => now(),
                'user' => user()->name, // The user that is loged
                'host' => request()->ip(), // The machine's host
                'action'     => $action,
            ]);

            return response()->json(['message' => 'Audit recorded successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


     // To upload a csv file // Esta aun no esta lista
     public function uploadFile(Request $request)
     {
         // Validamos que haya una consulta en el request
         $request->validate([
             'query' => 'required|string'
         ]);
 
         $query = $request->input('query');
 
         // Seguridad: Evitar consultas peligrosas
         $bloqueadas = ['DROP', 'TRUNCATE', 'ALTER','CREATE']; 
         foreach ($bloqueadas as $palabra) {
             if (stripos($query, $palabra) !== false) {
                 return response()->json(["error" => "Invalid operation"], 403);
             }
         }
 
         try {
             // Ejecutamos la consulta
             $result = DB::select($query);
             return response()->json($result, 200);
         } catch (\Exception $e) {
             return response()->json(["error" => $e->getMessage()], 400);
         }
     }



}