<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MainNotification extends Notification implements ShouldBroadcast
{
    protected array $payload;
    protected $notifiable;

    public function __construct(array $payload)//$notifiable
    {
        $this->payload     = $payload;
        // $this->notifiable  = $notifiable;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable)
    {
        // \Log::info('notifiable -- HERE', [$notifiable]);

        if (empty($this->payload['title'])) {
            return $this->payload;
        }
        
        return [
            'title'   => $this->payload['title'],
            'message' => $this->payload['message'],
            'url'     => $this->payload['url'],
        ];
    }

    // Si quieres customizar el mensaje...
    public function toBroadcast($notifiable)
    {
        // \Log::info('notifiable -- HERE', [$notifiable]);

        // Si no hay título, devuelvo un BroadcastMessage vacío
        if (empty($this->payload['title'])) {
            return new BroadcastMessage([]);
        }

        // Sólo los que sí tienen title llegan aquí
        return new BroadcastMessage([
            'title'   => $this->payload['title'],
            'message' => $this->payload['message'] ?? '',
            'url'     => $this->payload['url']     ?? '',
        ]);
        }
}
