<?php

namespace App\Notifications;

use App\Models\MaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly MaintenanceLog $log) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name   = $this->log->loggable?->name ?? 'Không xác định';
        $status = $this->statusLabel();

        return (new MailMessage)
            ->subject('Cập nhật sự cố: ' . $name)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('**' . $name . '** — trạng thái sự cố đã cập nhật.')
            ->line('**Trạng thái mới:** ' . $status)
            ->line('**Mô tả:** ' . $this->log->description)
            ->action('Xem chi tiết', url('/admin/maintenance'));
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return [
            'type'    => 'maintenance_status_changed',
            'log_id'  => $this->log->id,
            'message' => $name . ': ' . $this->statusLabel(),
        ];
    }

    private function statusLabel(): string
    {
        return match ($this->log->status) {
            'in_progress' => 'Đang được xử lý',
            'resolved'    => 'Đã giải quyết xong',
            default       => $this->log->status,
        };
    }
}
