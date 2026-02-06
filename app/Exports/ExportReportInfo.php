<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportReportInfo implements FromArray
{
    private array $campaignIds;
    private string $startTime;
    private string $endTime;

    public function __construct(array $campaignIds, string $startTime, string $endTime)
    {
        $this->campaignIds = array_values($campaignIds);
        $this->startTime   = $startTime;
        $this->endTime     = $endTime;
    }

    public function array(): array
    {
        // 1) Definí encabezados "fijos" antes/después de variables
        $fixedBeforeVars = [
            'Venta',
            'nombre_completo',
            'cedula',
            'telefono',
            'fecha',
            'campaña',
            'nivel1',
            'nivel2',
            'nivel3',
            'nivel4',
            'comentarios',
            'duracion_llamada',
            'fecha_monitoreo',
            'usuario',
            'auditor',
            'seguros_vendidos',
            'monto_pagar',
            'estado',
            // aquí irían las columnas dinámicas
        ];

        $fixedAfterVars = [
            'nota_final',
            'observaciones',
            'guid',
            'base',
            'fecha_llamada',
        ];

        $bindings = [
            'start' => $this->startTime,
            'end'   => $this->endTime,
        ];

        $campaignFilter = 'AND 1=0';
        if (!empty($this->campaignIds)) {
            $placeholders = [];
            foreach ($this->campaignIds as $idx => $id) {
                $key = "c{$idx}";
                $placeholders[] = ":{$key}";
                $bindings[$key] = $id;
            }
            $inList = implode(',', $placeholders);
            $campaignFilter = "AND ll.campaign_id IN ({$inList})";
        }

        $sql = "
            WITH
                ec_last AS (
                SELECT *
                FROM (
                    SELECT
                    ec.*,
                    ROW_NUMBER() OVER (
                        PARTITION BY ec.idVenta
                        ORDER BY ec.fecha_calidad DESC, ec.id DESC
                    ) AS rn
                    FROM evaluaciones_calidad ec
                ) t
                WHERE t.rn = 1
                ),
                ecv_last AS (
                SELECT *
                FROM (
                    SELECT
                    ecv.*,
                    ROW_NUMBER() OVER (
                        PARTITION BY ecv.idVenta
                        ORDER BY ecv.id DESC
                    ) AS rn
                    FROM estados_calidad_venta ecv
                ) t
                WHERE t.rn = 1
                )
                SELECT 
                CASE WHEN MAX(IFNULL(ll.isSale, 0)) = 1 THEN 'Sí' ELSE 'No' END AS Venta,
                MAX(IFNULL(CONCAT(IFNULL(l.nombre,''),' ',IFNULL(l.segundo_nombre,''),' ',IFNULL(l.apellido1,''),' ',IFNULL(l.apellido2,'')),'')) AS nombre_completo,
                MAX(IFNULL(l.cedula,'')) AS cedula,
                MAX(IFNULL(uc.`number`,'-')) AS telefono,
                MAX(IFNULL(ll.date_time,'')) AS fecha,
                MAX(IFNULL(c.name,'')) AS campaña,
                MAX(IFNULL(nt1.name,'')) AS nivel1,
                MAX(IFNULL(nt2.name,'')) AS nivel2,
                MAX(IFNULL(nt3.name,'')) AS nivel3,
                MAX(IFNULL(nt4.name,'')) AS nivel4,
                MAX(IFNULL(ll.notes,'')) AS comentarios,
                MAX(IFNULL(CAST(SEC_TO_TIME(uc.duration) AS TIME),'00:00:00')) AS duracion_llamada,
                MAX(IFNULL(ec_last.fecha_calidad,'N/A')) AS fecha_monitoreo,
                MAX(IFNULL(u.`user`,'')) AS usuario,
                MAX(IFNULL(au.`user`,'N/A')) AS auditor,
                IFNULL(GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ' | '), '-') AS seguros_vendidos,
                MAX(IFNULL(v.montoTotal,'')) AS monto_pagar,
                MAX(IFNULL(ecv_last.estado,'')) AS estado,
                MAX(IFNULL(ec_last.variables,'-')) AS variables,
                MAX(IFNULL(ecv_last.nota_estado,'0')) AS nota_final,
                MAX(IFNULL(ec_last.comentarios,'-')) AS observaciones,
                MAX(IFNULL(uc.guid,'-')) AS guid,
                MAX(IFNULL(l.nombreBase,'')) AS base,
                MAX(IFNULL(COALESCE(
                    STR_TO_DATE(uc.`date`, '%Y-%m-%d %H:%i:%s'),
                    STR_TO_DATE(uc.`date`, '%Y-%m-%dT%H:%i:%s'),
                    STR_TO_DATE(uc.`date`, '%d/%m/%Y %H:%i:%s'),
                    uc.created_at
                ),'')) AS fecha_llamada
                FROM lead_logs ll
                INNER JOIN leads     l  ON ll.lead_id     = l.id
                INNER JOIN campaigns c  ON ll.campaign_id = c.id
                INNER JOIN users     u  ON ll.user_id     = u.id
                LEFT JOIN notes_typifications nt1 ON ll.notes_typification_id_1 = nt1.id
                LEFT JOIN notes_typifications nt2 ON ll.notes_typification_id_2 = nt2.id
                LEFT JOIN notes_typifications nt3 ON ll.notes_typification_id_3 = nt3.id
                LEFT JOIN notes_typifications nt4 ON ll.notes_typification_id_4 = nt4.id
                LEFT JOIN ventas v ON ll.id = v.idNota
                LEFT JOIN productos_ventas pv ON v.idVenta = pv.idVenta
                LEFT JOIN products p ON p.id = pv.idProducto
                LEFT JOIN ecv_last ON ecv_last.idVenta = v.idVenta
                LEFT JOIN ec_last  ON ec_last.idVenta  = v.idVenta
                LEFT JOIN users au ON au.id = ec_last.creado_por
                LEFT JOIN uphone_calls uc ON uc.guid = ll.guid 
                WHERE ll.log_type = 'notes'
                {$campaignFilter}
                AND ll.date_time BETWEEN :start AND :end
                GROUP BY ll.id
                ORDER BY MAX(ll.date_time) ASC
                LIMIT 10000;
        ";

        $rows = DB::select($sql, $bindings);

        $varColsByKey = []; // key => header
        foreach ($rows as $row) {
            $raw = $row->variables ?? null;
            if (!$raw || $raw === '-' ) {
                continue;
            }

            $arr = json_decode($raw, true);
            if (!is_array($arr)) {
                continue;
            }

            foreach ($arr as $v) {
                if (!is_array($v)) continue;

                $key = $v['xid'] ?? ($v['id'] ?? null);
                if ($key === null) continue;

                $nombre = trim((string)($v['nombre'] ?? ''));
                $desc   = trim((string)($v['descripcion'] ?? ''));
                $valor   = trim((string)($v['nota_maxima'] ?? ''));

                $header = $nombre;
                if ($desc !== '') {
                    $header .= " - {$desc}";
                    if ($valor !== '' || $valor !== null || $valor >= 0) {
                        $header .= " ({$valor}pts)";
                    }
                }

                if (!isset($varColsByKey[$key])) {
                    $varColsByKey[$key] = $header !== '' ? $header : ("variable_{$key}");
                }
            }
        }

        $dynamicVarHeaders = array_values($varColsByKey);
        $headers = array_merge($fixedBeforeVars, $dynamicVarHeaders, $fixedAfterVars);

        $data = [];
        $data[] = $headers;

        foreach ($rows as $row) {
            $markedByKey = [];

            $raw = $row->variables ?? null;
            if ($raw && $raw !== '-') {
                $arr = json_decode($raw, true);
                if (is_array($arr)) {
                    foreach ($arr as $v) {
                        if (!is_array($v)) continue;
                        $key = $v['xid'] ?? ($v['id'] ?? null);
                        if ($key === null) continue;

                        $marcada = $v['marcada'] ?? false;
                        $markedByKey[$key] = $marcada ? '✖' : '✔';
                    }
                }
            }

            $line = [];

            // columnas fijas antes de variables (toman directo del row)
            foreach ($fixedBeforeVars as $h) {
                $line[] = isset($row->$h) ? $row->$h : '';
            }

            // columnas dinámicas: si no existe en la fila, vacío (o 'No' si preferís)
            foreach ($varColsByKey as $key => $header) {
                $line[] = $markedByKey[$key] ?? ''; // poné 'No' si querés default
            }

            // columnas fijas después de variables
            foreach ($fixedAfterVars as $h) {
                $line[] = isset($row->$h) ? $row->$h : '';
            }

            $data[] = $line;
        }

        return $data;
    }

}
