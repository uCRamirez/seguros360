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
        $columns = array_filter($columns, fn($col) => in_array($col, 
            ['cedula','nombre','segundo_nombre','apellido1','apellido2','tel1','tel2','tel3','tel4','tel5','tel6','email','provincia_voto','hijos','estadoCivil','tipo_plan','fechaVencimiento','tarjeta','tipo_tarjeta','emisor','ultimos_digitos','mes_carga','anno_carga','foco_venta','fechaNacimiento','genero','nacionalidad','agente']
        ));
        return response()->json($columns);
    }
}
