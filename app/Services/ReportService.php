<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Room utilization: percentage of business hours booked (approved) per room.
     *
     * @return Collection<int, array{room_id: int, name: string, utilization_pct: float}>
     */
    public function roomUtilization(Carbon $from, Carbon $to): Collection
    {
        $totalHours = $from->diffInDays($to) * 14; // 07:00–21:00 = 14h/day

        $diffExpr = DB::connection()->getDriverName() === 'sqlite'
            ? DB::raw('SUM((julianday(end_time) - julianday(start_time)) * 24) as total_hours')
            : DB::raw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) / 60 as total_hours');

        $bookings = Booking::where('bookable_type', Room::class)
            ->where('status', 'approved')
            ->whereBetween('start_time', [$from, $to])
            ->select('bookable_id', $diffExpr)
            ->groupBy('bookable_id')
            ->get()
            ->keyBy('bookable_id');

        return Room::all()->map(function ($room) use ($bookings, $totalHours) {
            $bookedHours = $bookings->get($room->id)?->total_hours ?? 0;
            return [
                'room_id'         => $room->id,
                'name'            => $room->name,
                'building'        => $room->building,
                'booked_hours'    => round($bookedHours, 1),
                'utilization_pct' => $totalHours > 0 ? round(($bookedHours / $totalHours) * 100, 1) : 0,
            ];
        })->sortByDesc('utilization_pct')->values();
    }

    /**
     * Equipment usage frequency grouped by category.
     *
     * @return Collection<int, array{category: string, booking_count: int}>
     */
    public function equipmentUsage(Carbon $from, Carbon $to): Collection
    {
        return DB::table('booking_equipment')
            ->join('equipment', 'booking_equipment.equipment_id', '=', 'equipment.id')
            ->join('equipment_categories', 'equipment.category_id', '=', 'equipment_categories.id')
            ->join('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'approved')
            ->whereBetween('bookings.start_time', [$from, $to])
            ->select('equipment_categories.name as category', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('equipment_categories.id', 'equipment_categories.name')
            ->orderByDesc('booking_count')
            ->get();
    }

    /**
     * @return array{total: int, pending: int, approved: int, rejected: int, cancelled: int}
     */
    public function bookingSummary(Carbon $from, Carbon $to): array
    {
        $counts = Booking::whereBetween('start_time', [$from, $to])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total'     => $counts->sum(),
            'pending'   => $counts->get('pending', 0),
            'approved'  => $counts->get('approved', 0),
            'rejected'  => $counts->get('rejected', 0),
            'cancelled' => $counts->get('cancelled', 0),
        ];
    }

    /**
     * @return Collection<int, array{user_id: int, name: string, count: int}>
     */
    public function topUsers(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->whereBetween('bookings.start_time', [$from, $to])
            ->whereIn('bookings.status', ['approved', 'pending'])
            ->select('users.id as user_id', 'users.name', DB::raw('COUNT(*) as count'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    public function exportCsv(string $reportType, Carbon $from, Carbon $to): string
    {
        $headerMap = [
            'utilization' => ['room_id' => 'Mã phòng', 'name' => 'Tên phòng', 'building' => 'Toà nhà', 'booked_hours' => 'Giờ đã đặt', 'utilization_pct' => 'Tỷ lệ sử dụng (%)'],
            'summary'     => ['total' => 'Tổng', 'pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', 'cancelled' => 'Đã huỷ'],
            'top_users'   => ['user_id' => 'Mã người dùng', 'name' => 'Họ tên', 'count' => 'Số booking'],
        ];

        $rows = match ($reportType) {
            'utilization' => $this->roomUtilization($from, $to)->toArray(),
            'summary'     => [$this->bookingSummary($from, $to)],
            'top_users'   => $this->topUsers($from, $to)->toArray(),
            default       => [],
        };

        if (empty($rows)) {
            return '';
        }

        $lines   = [];
        $colKeys = array_keys((array) $rows[0]);
        $labels  = $headerMap[$reportType] ?? [];
        $headers = array_map(fn ($k) => $labels[$k] ?? $k, $colKeys);
        $lines[] = implode(',', array_map(fn ($h) => '"' . $h . '"', $headers));

        foreach ($rows as $row) {
            $lines[] = implode(',', array_map(
                fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"',
                (array) $row
            ));
        }

        // UTF-8 BOM giúp Excel nhận diện encoding đúng
        return "\xEF\xBB\xBF" . implode("\r\n", $lines);
    }
}
