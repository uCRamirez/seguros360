<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class MainNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $notficationData;

    public function __construct(array $notficationData)
    {
        $this->notficationData = $notficationData;
    }

    public function via($notifiable)
    {
        // Añadimos 'broadcast' además de database y mail
        $via = ['database', 'broadcast'];

        if (! empty($this->notficationData['mail']['isAbleToSend'])) {
            $via[] = 'mail';
        }

        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->notficationData['mail']['title'])
            ->greeting('Hello!')
            ->line($this->notficationData['mail']['content']);
    }

    public function toArray($notifiable)
    {
        return $this->notficationData;
    }

    /**
     * Canal en el que se va a emitir el evento de broadcast.
     */
    public function broadcastOn()
    {
        return new PrivateChannel("App.Models.User.{$notifiable->id}");
    }

    /**
     * Payload que recibe el frontend via Echo.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id'   => $this->id,
            'data' => $this->notficationData,
        ]);
    }
}
