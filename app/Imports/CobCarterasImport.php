<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Campaign;
use App\Models\User;
use App\Models\TypeDocument;
use App\Models\CobranzasCarteras;
use App\Models\CobranzasBasesCarteras;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\ApiResponse;

class CobCarterasImport implements ToArray, WithHeadingRow
{
    protected $requeridos = [
        'identificador_cartera',
        'identificador_cliente',
        'proyecto',
        'total_deuda',
        'monto_pagado',
        'fecha_deuda'
    ];

    protected $nombre_base = '';

    public function __construct($nombre_base){
        $this->nombre_base = $nombre_base;
    }

    public function array(array $data)
    {
        $bases = CobranzasBasesCarteras::where('nombre_base', $this->nombre_base)->get();
        if(!$bases->isEmpty()) throw new ApiException("The base name already exists.");;

        return DB::transaction(function () use ($data) {

            $proyectos = Campaign::where('active', 1)->where('form','Cobranzas')->get();
            if($proyectos->isEmpty()) throw new ApiException("The form 'Cobranzas' is not in use.");

            // Validar obligatorios
            foreach ($data as $index => $row) {
                foreach ($this->requeridos as $campo) {
                    if (
                        !array_key_exists($campo, $row) ||
                        is_null($row[$campo]) ||
                        (is_string($row[$campo]) && trim($row[$campo]) === '')
                    ) {
                        throw new ApiException("Row ".($index + 2).": '{$campo}' is required.");
                    }
                    if ($campo === 'proyecto' && $proyectos->where('name', $row['proyecto'])->isEmpty()) {
                        throw new ApiException("Row ".($index + 2).": '{$campo}' invalid.");
                    }
                    if ($campo === 'total_deuda' || $campo === 'monto_pagado') {
                        $valor = is_string($row[$campo]) ? trim($row[$campo]) : $row[$campo];
                        if (is_string($valor)) $valor = str_replace(',', '', $valor);
                        if (!is_numeric($valor)) throw new ApiException("Row ".($index + 2).": '{$campo}' must be numerical.");
                    }
                    if ($campo === 'fecha_deuda') {
                        $valor = is_string($row[$campo]) ? trim($row[$campo]) : $row[$campo];

                        $formatos = ['Y-m-d', 'd/m/Y', 'd-m-Y'];

                        $valida = false;
                        foreach ($formatos as $formato) {
                            $fecha = \DateTime::createFromFormat($formato, (string) $valor);

                            $errores = \DateTime::getLastErrors();
                            if ($errores === false) {
                                $errores = ['warning_count' => 0, 'error_count' => 0];
                            }

                            if ($fecha && $errores['warning_count'] === 0 && $errores['error_count'] === 0) {
                                $valida = true;
                                break;
                            }
                        }

                        if (!$valida) throw new ApiException("Row ".($index + 2).": '{$campo}' must be a valid date (Y-m-d, d/m/Y or d-m-Y).");
                    }
                }
            }

            // Guardar los registros
            foreach ($data as $index => $row) {
                $proyecto = $proyectos->where('name', $row['proyecto'])->first();

                if(!$proyecto) $proyecto = null;

                $cartera = CobranzasCarteras::where('identificador', $row['identificador_cartera'])
                    ->where('identificador_cliente', $row['identificador_cliente'])
                    ->where('campaign_id', $proyecto->id ?? null)
                    ->first();

                if (!$cartera) {
                    $cartera = new CobranzasCarteras();
                    $cartera->identificador = $row['identificador_cartera'];
                    $cartera->identificador_cliente = $row['identificador_cliente'];
                    $cartera->campaign_id   = $proyecto->id ?? null;
                    $cartera->fecha_carga   = date('Y-m-d H:i:s');
                }

                $cartera->total_deuda   = $row['total_deuda'] ?? 0;
                $cartera->monto_pagado      = $row['monto_pagado'] ?? 0;
                $cartera->intereses         = $row['intereses'] ?? 0;
                $cartera->cantidad_pagos    = $row['cantidad_pagos'] ?? 0;
                $cartera->dias_mora         = $row['dias_mora'] ?? 0;
                $cartera->clasificacion_cartera = $row['clasificacion_cartera'] ?? '';
                $cartera->fecha_deuda       = $row['fecha_deuda'] ?? null;
                $cartera->estado_judicial   =  strtolower($row['estado_judicial']) === 'si' || strtolower($row['estado_judicial']) === 'sí' ? 1 : 0;
                $cartera->nombre_base       = $this->nombre_base;
                $cartera->completada        = 1;
                $cartera->activo            = 1;
                $cartera->save();
            }

            Common::guardarBase($this->nombre_base, count($data), new CobranzasBasesCarteras);

            return true;
        });
    }
}
