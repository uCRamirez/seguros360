<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendLeadMail extends Notification implements ShouldQueue
{
    use Queueable;

    public $mailSubject;
    public $mailMessage;
    public $fromName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $message, $fromName = null)
    {
        $this->mailSubject = $subject;
        $this->mailMessage = $message;
        $this->fromName = $fromName ?? config('mail.from.name');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), $this->fromName)
            ->subject($this->mailSubject)
            ->markdown('mail.lead_mail', ['content' => $this->mailMessage]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
