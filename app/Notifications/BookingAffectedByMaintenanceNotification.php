<?php

namespace App\Notifications;

use App\Models\MaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAffectedByMaintenanceNotification extends Notification implements ShouldQueue
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
            ->subject('Thông báo bảo trì: ' . $name)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Phòng/thiết bị bạn đã đặt đang gặp sự cố và tạm thời không khả dụng.')
            ->line('**' . $name . '** — ' . $this->log->description)
            ->line('Vui lòng liên hệ quản trị viên hoặc đặt lại lịch sang phòng/thiết bị khác.')
            ->action('Xem lịch đặt của tôi', url('/bookings'));
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return [
            'type'    => 'booking_affected_by_maintenance',
            'log_id'  => $this->log->id,
            'message' => $name . ' đang bảo trì — booking của bạn có thể bị ảnh hưởng.',
        ];
    }
}
