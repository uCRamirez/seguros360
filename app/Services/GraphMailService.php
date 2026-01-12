<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class GraphMailService
{
    public function getToken(): string
    {
        $tenant = env('AZURE_TENANT_ID');
        $clientId = env('AZURE_CLIENT_ID');
        $secret = env('AZURE_CLIENT_SECRET');

        $resp = Http::asForm()->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $secret,
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default',
        ])->throw();

        return $resp->json('access_token');
    }

    public function sendMail(string $fromUpn, array $to, string $subject, string $html)
    {
        $token = $this->getToken();

        return Http::withToken($token)
            ->post("https://graph.microsoft.com/v1.0/users/".rawurlencode($fromUpn)."/sendMail", [
                'message' => [
                    'subject' => $subject,
                    'body' => ['contentType' => 'HTML', 'content' => $html],
                    'toRecipients' => collect($to)->map(fn($addr) => ['emailAddress' => ['address' => $addr]])->values(),
                ],
                'saveToSentItems' => true,
            ])->throw();
    }
}
