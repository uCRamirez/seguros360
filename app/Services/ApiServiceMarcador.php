<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;
use Illuminate\Support\Facades\Log;

class ApiServiceMarcador
{
        
    protected string $subdomain;
    protected string $baseUrl;
    protected string $basicToken;

    public function __construct()
    {
        $this->subdomain = env('UCONTACT_SUB_DOMINIO');
        $this->baseUrl = "https://{$this->subdomain}.ucontactcloud.com/Integra/resources/Dialers/ScheduleDialerCall";
        $this->basicToken = env('UCONTACT_API_KEY_MARCADORES');
    }

    /**
     * Programa una llamada en el marcador UContact Cloud
     * 
     * @param array $parameters
     * @return array
     * 
     * Ejemplo de parámetros:
     * [
     *     'calldate' => '2026-01-29 15:00:00',
     *     'campaign' => 'XXXXX->',
     *     'destination' => '098344484',
     *     'alternatives' => '099124484:099121212', // opcional
     *     'agentphone' => '1001', // opcional, si Progresivo
     *     'data' => 'Par1=Val1:Par2=Val2' // opcional, para Flujos y Formularios
     * ]
     */
    public function scheduleCall(array $parameters): array
    {
        $logger = Log::channel('apis');
        try {
            $validar = $this->validateRequiredParameters($parameters);
            if (!$validar) return false;

            $callschedule = [
                'calldate' => $parameters['calldate'],
                'campaign' => $parameters['campaign'],
                'destination' => $parameters['destination'],
            ];

            if (!empty($parameters['alternatives'])) {
                $callschedule['alternatives'] = $parameters['alternatives'];
            }
            if (!empty($parameters['agentphone'])) {
                $callschedule['agentphone'] = $parameters['agentphone'];
            }
            if (!empty($parameters['data'])) {
                $callschedule['data'] = $parameters['data'];
            }else {
                $callschedule['data'] = "";
            }

            $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $this->basicToken
                ])
                ->asForm()
                ->post(
                    $this->baseUrl,
                    [
                        'callschedule' => json_encode($callschedule)
                    ]
                );

            \Log::info($response);

            return $this->handleResponse($response);

        } catch (Exception $e) {
            $logger->info('Error en envio API: ' . $e->getMessage());
            return [
                'success' => false,
                'code' => 0,
                'message' => 'Error: ' . $e->getMessage(),
                'error' => $e
            ];
        }
    }

    /**
     * Programa múltiples llamadas
     * 
     * @param array $callsList
     * @return array
     */
    public function scheduleMultipleCalls(array $callsList): array
    {
        $results = [];

        foreach ($callsList as $index => $callData) {
            $results[$index] = $this->scheduleCall($callData);
        }

        return $results;
    }

    /**
     * Valida los parámetros requeridos
     * 
     * @param array $parameters
     * @throws Exception
     */
    protected function validateRequiredParameters(array $parameters): bool
    {
        $logger = Log::channel('apis');
        $required = ['calldate', 'campaign', 'destination'];

        foreach ($required as $param) {
            if (empty($parameters[$param])) {
                $logger->info("#------------------------------------------------------------------------------------------#");
                $logger->info("Parámetro requerido faltante: {$param}");
                return false;
            }
        }

        if (!$this->isValidDateTime($parameters['calldate'])) {
            $logger->info("Formato de fecha inválido. Use: YYYY-MM-DD HH:MM:SS");
            return false;
        }

        return true;
    }

    /**
     * Valida el formato de fecha y hora
     * 
     * @param string $datetime
     * @return bool
     */
    protected function isValidDateTime(string $datetime): bool
    {
        $format = 'Y-m-d H:i:s';
        $d = \DateTime::createFromFormat($format, $datetime);
        return $d && $d->format($format) === $datetime;
    }

    /**
     * Procesa la respuesta de la API
     * 
     * @param Response $response
     * @return array
     */
    protected function handleResponse(Response $response): array
    {
        try {
            // uC retorna 1 o 0
            $body = $response->body();
            $code = (int) trim($body);

            if ($response->successful() && $code === 1) {
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'code' => 0,
                'message' => 'Error al procesar respuesta: ' . $e->getMessage(),
                'response' => $response->body()
            ];
        }
    }
}
