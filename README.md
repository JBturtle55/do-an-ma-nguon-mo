# Hệ thống Quản lý và Đặt lịch Phòng Lab / Thiết bị

Đồ án môn học Mã nguồn mở — Hệ thống web quản lý và đặt lịch phòng thực hành, thiết bị dành cho trường học / trung tâm đào tạo.

## Tính năng

- **Đặt lịch phòng & thiết bị** — chọn Ca nhanh (Ca 1–5) hoặc tự chọn giờ, kiểm tra conflict real-time
- **3 vai trò người dùng** — Admin, Giảng viên với quyền hạn khác nhau
- **Duyệt booking** — Admin xem danh sách chờ duyệt, duyệt hoặc từ chối kèm ghi chú
- **Lịch tổng quan** — giao diện thời khóa biểu đại học (15 tiết × 7 ngày)
- **Thông báo** — email + database notification khi booking được tạo/duyệt/từ chối
- **Quản lý bảo trì** — báo cáo sự cố phòng/thiết bị, theo dõi trạng thái
- **Báo cáo thống kê** — tỷ lệ sử dụng phòng/thiết bị, xuất CSV

## Tech Stack

| | |
|---|---|
| Backend | Laravel 11 (PHP 8.3) |
| Database | MySQL 8 |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Auth | Laravel Breeze + Spatie Laravel Permission |
| Build tool | Vite |

## Yêu cầu hệ thống

- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL 8

## Cài đặt

```bash
# 1. Clone repo
git clone https://github.com/JBturtle55/do-an-ma-nguon-mo.git
cd do-an-ma-nguon-mo

# 2. Cài PHP dependencies
composer install

# 3. Cài Node dependencies
npm install

# 4. Cấu hình môi trường
cp .env.example .env
php artisan key:generate
```

Sửa file `.env`:
```
DB_DATABASE=lab_scheduler
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

```bash
# 5. Tạo database và seed dữ liệu mẫu
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Khởi động server
php artisan serve
```

Truy cập tại `http://localhost:8000`

## Tài khoản mẫu

| Role | Email | Password |
|---|---|---|
| Admin | admin@lab.test | password |
| Giảng viên | lecturer@lab.test | password |
| Sinh viên | student@lab.test | password |

## Cấu trúc thư mục chính

```
app/
├── Http/Controllers/     # Controllers theo role (Admin/, Api/)
├── Models/               # Eloquent models
├── Services/             # Business logic (BookingService, AvailabilityService...)
├── Policies/             # Authorization policies
└── Notifications/        # Email + database notifications

resources/
├── views/                # Blade templates
│   ├── admin/            # Giao diện quản trị
│   ├── bookings/         # Đặt lịch
│   └── components/       # Reusable components
└── js/
    ├── timetable.js      # Lịch thời khóa biểu tự viết
    └── calendar.js       # FullCalendar wrapper

database/
├── migrations/           # Schema database
└── seeders/              # Dữ liệu mẫu
```

## Luồng đặt lịch

```
User chọn phòng/thiết bị + thời gian
  → Kiểm tra conflict real-time (API)
  → Submit → DB transaction + pessimistic lock
  → Tạo booking (status: pending)
  → Admin nhận thông báo → Duyệt / Từ chối
  → User nhận kết quả qua thông báo
```

