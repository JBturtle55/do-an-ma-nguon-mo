<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification implements ShouldQueue
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
            ->subject('Nhắc lịch: ' . $this->booking->title . ' (còn 24h)')
            ->greeting('Xin chào ' . $notifiable->name)
            ->line('Nhắc nhở: Bạn có lịch thực hành vào ngày mai.')
            ->line('**' . $this->booking->title . '**')
            ->line('Thời gian: ' . $this->booking->start_time->format('d/m/Y H:i') . ' – ' . $this->booking->end_time->format('H:i'))
            ->action('Xem chi tiết', url('/bookings/' . $this->booking->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'booking_reminder',
            'booking_id' => $this->booking->id,
            'title'      => $this->booking->title,
            'start_time' => $this->booking->start_time->toIso8601String(),
            'message'    => 'Nhắc lịch: "' . $this->booking->title . '" diễn ra vào ' . $this->booking->start_time->format('d/m/Y H:i'),
        ];
    }
}
