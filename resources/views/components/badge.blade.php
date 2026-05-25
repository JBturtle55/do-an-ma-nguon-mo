@props(['status', 'size' => 'sm'])

@php
$colors = [
    'pending'       => 'bg-yellow-100 text-yellow-800',
    'approved'      => 'bg-green-100 text-green-800',
    'rejected'      => 'bg-red-100 text-red-800',
    'cancelled'     => 'bg-gray-100 text-gray-600',
    'available'     => 'bg-green-100 text-green-800',
    'maintenance'   => 'bg-orange-100 text-orange-800',
    'unavailable'   => 'bg-red-100 text-red-800',
    'open'          => 'bg-red-100 text-red-800',
    'in_progress'   => 'bg-blue-100 text-blue-800',
    'resolved'      => 'bg-green-100 text-green-800',
    'admin'         => 'bg-purple-100 text-purple-800',
    'lecturer'      => 'bg-blue-100 text-blue-800',
    'student'       => 'bg-gray-100 text-gray-700',
    'lab'           => 'bg-blue-100 text-blue-700',
    'classroom'     => 'bg-green-100 text-green-700',
    'workshop'      => 'bg-orange-100 text-orange-700',
];

$labels = [
    'pending'       => 'Chờ duyệt',
    'approved'      => 'Đã duyệt',
    'rejected'      => 'Từ chối',
    'cancelled'     => 'Đã huỷ',
    'available'     => 'Sẵn sàng',
    'maintenance'   => 'Bảo trì',
    'unavailable'   => 'Không dùng',
    'open'          => 'Mới báo',
    'in_progress'   => 'Đang xử lý',
    'resolved'      => 'Đã giải quyết',
    'admin'         => 'Quản trị',
    'lecturer'      => 'Giảng viên',
    'student'       => 'Sinh viên',
    'lab'           => 'Phòng Lab',
    'classroom'     => 'Phòng học',
    'workshop'      => 'Xưởng thực hành',
];

$color = $colors[$status] ?? 'bg-gray-100 text-gray-600';
$label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium $color"]) }}>
    {{ $label }}
</span>
