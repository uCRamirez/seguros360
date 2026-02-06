<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\CobranzasPagos;
use App\Models\CobranzasCarteras;
use App\Models\Campaign;
use App\Models\CobranzasBasesPagos;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\ApiResponse;

class CobPagosImport implements ToArray, WithHeadingRow
{
    protected $requeridos = [
        'proyecto',
        'identificador_cartera',
        'monto',
        'fecha_banco',
    ];

    protected $nombre_base = '';

    public function __construct($nombre_base){
        $this->nombre_base = $nombre_base;
    }

    public function array(array $data)
    {
        $bases = CobranzasBasesPagos::where('nombre_base', $this->nombre_base)->get();
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
                    if (
                        $campo === 'identificador_cartera' 
                        && CobranzasCarteras::where('identificador', $row['identificador_cartera'])
                            ->whereHas('campaign', function ($query) use ($row, $proyectos) {
                                $query->where('name', $row['proyecto'])
                                      ->whereIn('id', $proyectos->pluck('id')->toArray());
                            })->doesntExist()
                    ) {
                        throw new ApiException("Row ".($index + 2).": '{$campo}' not registered.");
                    }
                    if ($campo === 'monto') {
                        $valor = is_string($row[$campo]) ? trim($row[$campo]) : $row[$campo];
                        if (is_string($valor)) $valor = str_replace(',', '', $valor);
                        if (!is_numeric($valor)) throw new ApiException("Row ".($index + 2).": '{$campo}' must be numerical.");
                    }
                    if ($campo === 'fecha_banco') {
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

                $pago = new CobranzasPagos();
                $pago->identificador_cartera = $row['identificador_cartera'];
                $pago->monto = $row['monto'] ?? 0;
                $pago->detalle = $row['detalle'] ?? null;
                $pago->fecha_banco = $row['fecha_banco'] ?? null;
                $pago->nombre_base = $this->nombre_base ?? '';
                $pago->campaign_id = $proyecto->id ?? null;
                $pago->activo = 1;
                $pago->save();
            }

            Common::guardarBase($this->nombre_base, count($data), new CobranzasBasesPagos);

            return true;
        });
    }
}
