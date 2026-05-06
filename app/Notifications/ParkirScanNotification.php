<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ParkirScanNotification extends Notification
{
    use Queueable;

    public string $plat;
    public string $status;

    public function __construct(string $plat, string $status)
    {
        $this->plat = $plat;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $desc = match ($this->status) {
            'active'    => 'Check-in: ',
            'completed' => 'Check-out: ',
            'violation' => 'Pelanggaran: ',
        };

        return [
            'message' => $desc . $this->plat,
            'icon'    => $this->status == 'active' ? 'bi-box-arrow-in-down' : ($this->status == 'completed' ? 'bi-check-circle' : 'bi-exclamation-triangle'),
            'type'    => $this->status == 'active' ? 'info' : ($this->status == 'completed' ? 'success' : 'danger'),
        ];
    }
}
