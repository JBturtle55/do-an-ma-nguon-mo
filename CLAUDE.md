# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Hệ thống quản lý và đặt lịch phòng lab / thiết bị thực hành (Lab & Equipment Scheduling Management System) dành cho trường học / trung tâm đào tạo.

**Stack:** Laravel 11, MySQL 8, Blade + Tailwind CSS (với Alpine.js cho interactivity nhẹ), Vite.

---

## Deployment (Production)

- **URL:** `https://management.longvan.vn`
- **Web root:** `/home/system-management`
- **Nginx config:** `/etc/nginx/sites-enabled/management.conf` — reverse proxy tới `127.0.0.1:8000`
- **Laravel server:** chạy qua `php artisan serve --host=127.0.0.1 --port=8000` (nên dùng systemd service để auto-start)
- **Database:** MySQL 8, user `lab_user`, database `lab_scheduler`
- **Assets:** đã build vào `public/build/` (chạy `npm run build` sau khi thay đổi CSS/JS)
- **HTTPS:** SSL cert do Certbot quản lý. `URL::forceScheme('https')` được gọi trong `AppServiceProvider::boot()` khi `APP_URL` bắt đầu bằng `https://` để tránh Mixed Content

### Khởi động server

```bash
php artisan serve --host=127.0.0.1 --port=8000 &>> /var/log/laravel-serve.log &
```

### Systemd service (recommended)

```ini
# /etc/systemd/system/lab-management.service
[Unit]
Description=Lab Management Laravel App
After=network.target

[Service]
User=root
WorkingDirectory=/home/system-management
ExecStart=/usr/bin/php8.3 artisan serve --host=127.0.0.1 --port=8000
Restart=always
StandardOutput=append:/var/log/laravel-serve.log
StandardError=append:/var/log/laravel-serve.log

[Install]
WantedBy=multi-user.target
```

### Queue Worker (required for notifications)

Notifications implement `ShouldQueue` — must have a queue worker running or they won't deliver.

```bash
# Start queue worker (already set up as systemd service)
systemctl start lab-queue
systemctl enable lab-queue   # auto-start on boot

# Check status
systemctl status lab-queue
tail -f /var/log/laravel-queue.log
```

```ini
# /etc/systemd/system/lab-queue.service (already created)
[Unit]
Description=Lab Management Queue Worker
After=network.target mysql.service

[Service]
User=root
WorkingDirectory=/home/system-management
ExecStart=/usr/bin/php8.3 artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5
StandardOutput=append:/var/log/laravel-queue.log
StandardError=append:/var/log/laravel-queue.log

[Install]
WantedBy=multi-user.target
```

---

## Common Commands

```bash
# Cài đặt dependencies
composer install
npm install

# Copy và cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Migrate + seed dữ liệu mẫu
php artisan migrate --seed

# Build assets (production) — bắt buộc chạy sau khi thay đổi CSS/JS
npm run build

# Build assets (dev với hot-reload)
npm run dev

# Clear cache sau khi thay đổi .env hoặc config
php artisan config:clear && php artisan cache:clear

# Chạy toàn bộ test suite
php artisan test

# Chạy một test file cụ thể
php artisan test tests/Feature/Booking/BookingConflictTest.php

# Chạy theo tên test
php artisan test --filter="adjacent bookings do not conflict"

# Static analysis
./vendor/bin/phpstan analyse

# Code style fix
./vendor/bin/pint
```

---

## Architecture Overview

### Domain Logic (app/Services/)

Toàn bộ business logic nằm trong Service classes, **không** đặt trong Controller hay Model:

- `BookingService` — kiểm tra conflict lịch, tạo/hủy booking, gửi notification
- `AvailabilityService` — tính slot còn trống theo ngày/giờ cho room và equipment
- `ReportService` — thống kê tỷ lệ sử dụng, xuất báo cáo

Controllers chỉ nhận request → gọi Service → trả về response/view.

### Authorization (app/Policies/ + Roles)

Dùng **Spatie Laravel Permission** cho role/permission. Ba role chính:

| Role | Quyền |
|---|---|
| `admin` | Toàn quyền: quản lý phòng, thiết bị, người dùng, duyệt booking |
| `lecturer` | Đặt lịch, xem lịch của mình, quản lý lớp học |
| `student` | Đặt lịch (nếu được cho phép), xem lịch cá nhân |

Gate/Policy kiểm tra quyền trước khi gọi Service.

### Booking Conflict Detection

`AvailabilityService::checkConflict()` kiểm tra overlap bằng query:
```
existing.start_time < new.end_time AND existing.end_time > new.start_time
```
Luôn wrap trong DB transaction khi tạo booking để tránh race condition (dùng pessimistic lock `lockForUpdate()`).

### Notification Flow

Dùng Laravel Notifications (database + mail). Các event trigger notification:
- Booking tạo thành công → notify người đặt + admin
- Booking được duyệt/từ chối → notify người đặt
- Thiết bị báo hỏng → notify admin/kỹ thuật viên
- Nhắc lịch trước 24h → queued notification

Queue driver dùng `database` (dev) hoặc Redis (production).

---

## Database Schema (Key Tables)

```
users               — id, name, email, role (via Spatie)
rooms               — id, name, building, capacity, type (lab|classroom|workshop), status, image
equipment           — id, name, category_id, room_id(nullable), quantity, status, description
equipment_categories — id, name

bookings            — id, user_id, bookable_type, bookable_id, title, start_time, end_time,
                       purpose, status (pending|approved|rejected|cancelled), approved_by, notes
booking_equipment   — booking_id, equipment_id, quantity  (pivot khi đặt thiết bị kèm phòng)

maintenance_logs    — id, loggable_type, loggable_id, reported_by, description, status, resolved_at
schedules           — id, room_id, recurring_type (daily|weekly|none), day_of_week, start_time, end_time
                       (lịch cố định định kỳ ưu tiên cao hơn booking thường)
```

`bookings` dùng **polymorphic** (`bookable`) để đặt cả Room lẫn Equipment riêng lẻ.

---

## Frontend Conventions

- **Layout:** `resources/views/layouts/app.blade.php` (authenticated) và `layouts/guest.blade.php`
- **Components:** Blade components trong `resources/views/components/` — ưu tiên dùng anonymous components
- **Tailwind:** config tại `tailwind.config.js`, custom color dùng CSS variables trong `app.css`
- **Alpine.js:** dùng cho modal, dropdown, calendar date-picker — không dùng jQuery
- **Timetable view:** `resources/js/timetable.js` — university-style timetable (15 tiết × 7 ngày), thay thế FullCalendar trên hầu hết các trang. FullCalendar vẫn dùng cho `/rooms/{id}/schedule`.
- **Calendar lazy-load:** `app.js` export `window.initTimetable` và `window.initCalendar` qua dynamic import (`import('./timetable.js')`) để code-split tự động.

Không dùng Livewire hay Inertia — giữ stack đơn giản Blade + Alpine.

### Timetable (`resources/js/timetable.js`)

- 15 tiết rows (Tiết 1: 06:45–07:30 … Tiết 15: 19:30–20:15), 7 cột (Thứ 2–CN)
- `GROUP_AFTER = new Set([3, 6, 9, 12])` — đường kẻ dày phân Ca 1–5
- `rowspan` dùng để event trải qua nhiều tiết; grid pre-compute `type: 'event'|'span'|'empty'`
- Hover popup: module-level `_hideTimer` + `scheduleHide()`/`cancelHide()` — di chuột giữa event và popup không đóng popup
- Khởi tạo trong Blade: `x-data x-init="$nextTick(() => window.initTimetable('element-id', 'url'))"`

### Calendar Routes — quan trọng

Các route `/api/calendar/*` được đặt trong **`routes/web.php`** (không phải `api.php`) để hưởng session middleware. Laravel 11 `api.php` thiếu session middleware → AJAX fetch từ browser bị 401 dù đã đăng nhập.

```php
// routes/web.php — bên trong auth middleware group
Route::prefix('api/calendar')->name('api.calendar.')->group(function () {
    Route::get('/events',              [CalendarController::class, 'events'])->name('events');
    Route::get('/room/{room}/events',  [CalendarController::class, 'roomEvents'])->name('room-events');
    Route::get('/my-events',           [CalendarController::class, 'myEvents'])->name('my-events');
});
```

### Dashboard theo role

- **Admin** (`/admin/dashboard`): stats 6 card (gồm "Duyệt hôm nay" = `Booking::approved()->whereDate('updated_at', today())`), pending bookings table, lịch tổng quan (`api.calendar.events`).
- **Lecturer/Student** (`/dashboard`): stats 4 card cá nhân, quick actions, lịch cá nhân (`api.calendar.my-events`).
- `DashboardController::index()` redirect admin về `admin.dashboard` ngay đầu hàm.
- Sidebar: admin chỉ thấy "Admin Dashboard" ở đầu, không có mục "Đặt lịch" (Lịch đặt của tôi, Đặt lịch mới).

### Form đặt lịch (`bookings/create.blade.php`)

- **Multi-select Ca**: `selectedSlots: []` (array label), `selectSlot()` toggle, `updateTimesFromSlots()` tính `min(start)` và `max(end)` từ các Ca được chọn.
- Slots định nghĩa với `start`/`end` (string HH:MM), template dùng `slot.start + '–' + slot.end` (không phải `startLabel`/`endLabel`).
- Khi user sửa tay datetime input → `syncDateFromTime()` clear `selectedSlots`.

---

## Key Routes Structure

```
/                    — trang chủ / dashboard (redirect theo role)
/bookings            — danh sách booking của user
/bookings/create     — form đặt lịch mới (Ca nhanh multi-select)
/bookings/{id}       — chi tiết, duyệt/từ chối (admin)
/rooms               — danh sách phòng + trạng thái real-time
/rooms/{id}/schedule — lịch của phòng (FullCalendar view)
/equipment           — danh sách thiết bị
/api/calendar/*      — JSON events cho timetable (nằm trong web.php, auth middleware)
/admin/*             — quản lý người dùng, phòng, thiết bị, báo cáo (middleware: role:admin)
```

---

## Environment Variables cần thiết

```
APP_URL=https://management.longvan.vn   # phải có https:// để forceScheme hoạt động
DB_DATABASE=lab_scheduler
DB_USERNAME=lab_user
DB_PASSWORD=lab_pass_2024
MAIL_MAILER / MAIL_HOST / MAIL_FROM_ADDRESS   # cho notification email
QUEUE_CONNECTION=database                      # hoặc redis cho production
```

---

## Testing Approach

- **Feature tests** (`tests/Feature/`) — test HTTP endpoints qua `actingAs()`, dùng `RefreshDatabase`
- **Unit tests** (`tests/Unit/`) — test Service classes trực tiếp: `AvailabilityServiceTest`, `BookingServiceTest`, `ReportServiceTest`
- Cả Feature và Unit đều bind `TestCase` + `RefreshDatabase` trong `tests/Pest.php`
- Mỗi `beforeEach` phải gọi `app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions()` để tránh Spatie cache gây lỗi ngẫu nhiên giữa các test
- Khi tạo thời gian trong test, luôn dùng `->setSecond(0)` để tránh false conflict do seconds thừa
- Dùng `Notification::fake()` và `Queue::fake()` để không gửi mail/job thật
- **57 tests, 121 assertions** — tất cả pass
