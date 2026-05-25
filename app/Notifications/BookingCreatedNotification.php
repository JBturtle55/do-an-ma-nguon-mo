<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đặt lịch mới: ' . $this->booking->title)
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Yêu cầu đặt lịch mới đã được tạo.')
            ->line('**' . $this->booking->title . '**')
            ->line('Thời gian: ' . $this->booking->start_time->format('d/m/Y H:i') . ' – ' . $this->booking->end_time->format('H:i'))
            ->action('Xem chi tiết', url('/bookings/' . $this->booking->id))
            ->line('Trạng thái hiện tại: Chờ duyệt');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'booking_created',
            'booking_id' => $this->booking->id,
            'title'      => $this->booking->title,
            'status'     => $this->booking->status,
            'start_time' => $this->booking->start_time->toIso8601String(),
            'message'    => 'Yêu cầu đặt lịch "' . $this->booking->title . '" đang chờ duyệt.',
        ];
    }
}
