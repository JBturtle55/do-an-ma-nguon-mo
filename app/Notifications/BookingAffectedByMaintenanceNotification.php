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
            ->line('**' . $name . '** hiện đang trong quá trình bảo trì và tạm thời không thể đặt lịch.')
            ->line('**Lý do:** ' . $this->log->description)
            ->line('Vui lòng chọn phòng/thiết bị khác hoặc liên hệ quản trị viên để biết thêm thông tin.')
            ->action('Đặt lịch mới', url('/bookings/create'));
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->log->loggable?->name ?? 'Không xác định';

        return [
            'type'    => 'maintenance_notice',
            'log_id'  => $this->log->id,
            'message' => $name . ' đang bảo trì và tạm thời không thể đặt lịch.',
        ];
    }
}
