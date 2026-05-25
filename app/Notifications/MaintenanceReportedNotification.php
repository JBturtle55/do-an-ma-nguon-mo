<?php

namespace App\Notifications;

use App\Models\MaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceReportedNotification extends Notification implements ShouldQueue
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
            ->subject('Sự cố: ' . $name)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Có báo cáo sự cố/hỏng hóc mới.')
            ->line('**' . $name . '** đang gặp sự cố.')
            ->line('**Mô tả:** ' . $this->log->description)
            ->action('Xem chi tiết', url('/admin/maintenance'));
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return [
            'type'    => 'maintenance_reported',
            'log_id'  => $this->log->id,
            'message' => $name . ' đang gặp sự cố: ' . $this->log->description,
        ];
    }
}
