<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LoginFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $email;
    public string $ip;

    public function __construct(string $email, string $ip)
    {
        $this->email = $email;
        $this->ip = $ip;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Login gagal: ' . $this->email,
            'ip'      => $this->ip,
            'icon'    => 'bi-exclamation-triangle',
            'type'    => 'warning',
        ];
    }
}
