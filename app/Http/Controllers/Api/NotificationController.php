<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\User;
use App\Notifications\MainNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Api\Notification\IndexRequest;
use App\Http\Requests\Api\Notification\StoreRequest;
use App\Http\Requests\Api\Notification\UpdateRequest;
use App\Http\Requests\Api\Notification\DeleteRequest;
use Illuminate\Http\Request;


class NotificationController extends ApiBaseController
{

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

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
        \Log::info('makeNotification', [$request]);

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

        return response()->json(['status' => 'notificación enviada']);
    }


    public function sendToUsers(Request $request)
    {

        // \Log::info('$request', [$request]);

        $data = $request->all();

        // \Log::info('$data', [$data]);


        // Construye el payload para la notificación
        $payload = [
            'title'   => $data['title'],
            'message' => $data['message'],
            'url'     => $data['url'] ?? null,
            // 'mail'    => $data['mail'] ?? ['isAbleToSend' => false, 'title' => '', 'content' => ''],
        ];

        // Recupera los usuarios destino
        $users = User::whereIn('id', $data['user_ids'])->get();

        // Dispara la notificación (BD + broadcast)
        Notification::send($users, new MainNotification($payload));

        return response()->json([
            'status'   => 'ok',
            'sent_to'  => $users->pluck('id'),
        ]);
    }
}

