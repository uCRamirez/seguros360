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
    public $ccEmail;

    public function __construct($subject, $message, $fromName = null, $ccEmail = null)
    {
        $this->mailSubject = $subject;
        $this->mailMessage = $message;
        $this->fromName = $fromName ?? config('mail.from.name');
        $this->ccEmail = $ccEmail;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->from(config('mail.from.address'), $this->fromName)
            ->subject($this->mailSubject)
            ->markdown('mail.lead_mail', ['content' => $this->mailMessage]);

        if ($this->ccEmail) {
            $mail->withSymfonyMessage(function ($message) {
                $message->cc($this->ccEmail);
            });
        }

        return $mail;
    }


    public function toArray($notifiable)
    {
        return [];
    }
}
