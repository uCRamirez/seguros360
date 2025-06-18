<?php

use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí defines los canales a los que tu aplicación puede suscribirse
| utilizando Laravel Echo (o Pusher). En este ejemplo, por cada usuario
| habrá un canal privado llamado "App.Models.User.{id}".
|
*/

Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return (int)$user->id === (int)$id;
});
