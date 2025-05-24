<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;   
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Column\IndexRequest;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Schema;


class ColumnController extends ApiBaseController
{

    public function allColumns($table)
    {
        // valida que la tabla exista (opcional)
        if (! Schema::hasTable($table)) {
            return response()->json(['error' => 'Tabla no encontrada'], 404);
        }
        // obtiene todos los nombres de columna
        $columns = Schema::getColumnListing($table);
        $columns = array_filter($columns, fn($col) => in_array($col, ['cedula','nombre','apellido1','apellido2','tel1','tel2','email','edad','fechaNacimiento']));
        return response()->json($columns);
    }
}
