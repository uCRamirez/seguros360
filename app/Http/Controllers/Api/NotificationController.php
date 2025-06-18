<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\User;


class NotificationController extends ApiBaseController
{

    public function index()
    {
        $user = user();  

        return response()->json([
            'unread' => $user->unreadNotifications()->get(),
            // 'read'   => $user->readNotifications()->limit(20)->get(),
        ]);
    }


    public function markAllRead()
    {
        $user = user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function makeNotification(Request $request)
    {
        $user = user();

        $payload = [
            'title'   => 'Notificación de Prueba',
            'message' => 'Este es un mensaje de prueba en tiempo real',
            'url'     => '/dashboard',
            'mail'    => [
                'isAbleToSend' => false,
                'title'        => '',
                'content'      => '',
            ],
        ];

        $user->notify(new MainNotification($payload));

        // 4) Responde OK
        return response()->json(['status' => 'notificación enviada']);
    }
}

