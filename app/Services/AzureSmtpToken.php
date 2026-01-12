<?php

namespace App\Services;
use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;

class AzureSmtpToken
{
    public function getAccessToken(): string
    {
        $tenantId     = env('AZURE_TENANT_ID');
        $clientId     = env('AZURE_CLIENT_ID');
        $clientSecret = env('AZURE_CLIENT_SECRET');

        if (!$tenantId || !$clientId || !$clientSecret) {
            throw new \RuntimeException('AZURE_* incompletos en variables de entorno');
        }

        $http = new Client(['timeout' => 10]);

        try {
            $resp = $http->post("https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token", [
                'form_params' => [
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'scope'         => 'https://outlook.office365.com/.default',
                    'grant_type'    => 'client_credentials',
                ],
            ]);
            $data = json_decode((string) $resp->getBody(), true);

            // Loguear TODO el response como JSON legible
            \Log::info('data resp', $data);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            
            \Log::error('OAuth2 token error: '.$body);
            throw new \RuntimeException('No se pudo obtener token (ClientException)');
        }

        $data = json_decode((string) $resp->getBody(), true);

        if (empty($data['access_token'])) {
            throw new \RuntimeException('No access_token; respuesta: '.json_encode($data));
        }

        return $data['access_token'];
    }
}
