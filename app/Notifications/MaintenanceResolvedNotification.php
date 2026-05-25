<?php

namespace App\Notifications;

use App\Models\MaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly MaintenanceLog $log) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return (new MailMessage)
            ->subject('Sự cố đã được giải quyết: ' . $name)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Sự cố đã được khắc phục.')
            ->line('**Đối tượng:** ' . $name)
            ->line('**Mô tả sự cố:** ' . $this->log->description)
            ->line('Bạn có thể tiếp tục sử dụng bình thường.');
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return [
            'type'    => 'maintenance_resolved',
            'log_id'  => $this->log->id,
            'message' => 'Sự cố "' . $name . '" đã được giải quyết.',
        ];
    }
}
