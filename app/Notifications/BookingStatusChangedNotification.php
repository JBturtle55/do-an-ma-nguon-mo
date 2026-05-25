<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = match ($this->booking->status) {
            'approved'  => 'Đã duyệt ✓',
            'rejected'  => 'Bị từ chối ✗',
            'cancelled' => 'Đã huỷ',
            default     => $this->booking->status,
        };

        return (new MailMessage)
            ->subject('Cập nhật lịch: ' . $this->booking->title)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Yêu cầu đặt lịch của bạn đã được cập nhật.')
            ->line('**' . $this->booking->title . '** — ' . $statusLabel)
            ->when($this->booking->notes, fn ($m) => $m->line('Ghi chú: ' . $this->booking->notes))
            ->action('Xem chi tiết', url('/bookings/' . $this->booking->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'booking_status_changed',
            'booking_id' => $this->booking->id,
            'title'      => $this->booking->title,
            'status'     => $this->booking->status,
            'message'    => 'Yêu cầu đặt lịch "' . $this->booking->title . '" đã được cập nhật thành: ' . $this->booking->status,
        ];
    }
}
