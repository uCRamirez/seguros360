<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Campaign;
use App\Models\User;
use App\Models\TypeDocument;
use App\Models\CobranzasClientes;
use App\Models\CobranzasBasesClientes;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\ApiResponse;

class CobClientesImport implements ToArray, WithHeadingRow
{
    protected $requeridos = [
        'identificador',
        'nombre_completo',
        'tel1',
        'proyecto',
    ];

    protected $nombre_base = '';

    public function __construct($nombre_base){
        $this->nombre_base = $nombre_base;
    }

    public function array(array $data)
    {
        $bases = CobranzasBasesClientes::where('nombre_base', $this->nombre_base)->get();
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
                }
            }

            // Guardar los registros
            foreach ($data as $index => $row) {
                $proyecto = $proyectos->where('name', $row['proyecto'])->first();
                $agente = User::where('user', $row['agente'])->first();
                $tipo_identificador = TypeDocument::where('name', $row['tipo_identificador'] ?? null)->first();

                if(!$proyecto) $proyecto = null;
                if(!$agente) $agente = null;
                if(!$tipo_identificador) $tipo_identificador = null;

                $cliente = CobranzasClientes::where('identificador', $row['identificador'])
                    ->where('campaign_id', $proyecto->id ?? null)
                    ->first();

                if (!$cliente) {
                    $cliente = new CobranzasClientes();
                }

                $cliente->campaign_id = $proyecto->id ?? null;
                $cliente->identificador = $row['identificador'];
                $cliente->tipo_identificador = $tipo_identificador->id ?? null;
                $cliente->nombre_completo = $row['nombre_completo'];
                $cliente->email = $row['email']  ?? null;
                $cliente->email2 = $row['email2']  ?? null;
                $cliente->tel1 = $row['tel1']  ?? null;
                $cliente->tel2 = $row['tel2']  ?? null;
                $cliente->tel3 = $row['tel3']  ?? null;
                $cliente->tel4 = $row['tel4']  ?? null;
                $cliente->tel5 = $row['tel5']  ?? null;
                $cliente->tel6 = $row['tel6']  ?? null;
                $cliente->provincia = $row['provincia']  ?? null;
                $cliente->canton = $row['canton']  ?? null;
                $cliente->distrito = $row['distrito']  ?? null;
                $cliente->direccion = $row['direccion']  ?? null;
                $cliente->nombre_base = $this->nombre_base  ?? '';
                $cliente->asignado_a = $agente->id ?? null;
                $cliente->activo = 1;
                $cliente->save();
            }

            Common::guardarBase($this->nombre_base, count($data), new CobranzasBasesClientes);

            return true;
        });
    }
}
