<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab Scheduler — Hệ thống quản lý phòng lab & thiết bị</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased">

    {{-- Nav --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900 text-lg">Lab Scheduler</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">Vào hệ thống</a>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary">Đăng nhập</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">Đăng ký</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="bg-white">
        <div class="max-w-6xl mx-auto px-6 py-20 text-center">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Hệ thống quản lý dành cho trường học & trung tâm đào tạo
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-5">
                Quản lý phòng lab &<br>thiết bị thực hành
            </h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-8">
                Đặt lịch phòng học, phòng thực hành và thiết bị một cách dễ dàng.
                Hệ thống tự động kiểm tra xung đột, thông báo realtime và theo dõi tình trạng bảo trì.
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-6 py-2.5">Vào hệ thống</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary px-6 py-2.5">Bắt đầu ngay</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary px-6 py-2.5">Tạo tài khoản</a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    {{-- Stats strip --}}
    <section class="border-y border-gray-200 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6 py-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <div class="text-3xl font-bold text-blue-600">3</div>
                <div class="text-sm text-gray-500 mt-1">Vai trò người dùng</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-green-600">5</div>
                <div class="text-sm text-gray-500 mt-1">Ca học mỗi ngày</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-purple-600">57</div>
                <div class="text-sm text-gray-500 mt-1">Test cases</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-indigo-600">100%</div>
                <div class="text-sm text-gray-500 mt-1">Kiểm tra xung đột</div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-10">Tính năng chính</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Đặt lịch thông minh</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Chọn ca học nhanh hoặc tùy chỉnh thời gian. Hệ thống tự động kiểm tra xung đột trước khi xác nhận.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Phân quyền theo vai trò</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Ba cấp quyền: Admin quản lý toàn hệ thống, Giảng viên và Sinh viên tự quản lý lịch cá nhân.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Thông báo realtime</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Nhận thông báo ngay khi booking được duyệt, từ chối, hoặc khi phòng có sự cố bảo trì.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Báo cáo & thống kê</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Xem tỷ lệ sử dụng phòng, top người dùng, thống kê theo danh mục thiết bị. Xuất CSV cho Excel.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Quản lý bảo trì</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Theo dõi sự cố phòng/thiết bị từ lúc báo cáo đến khi giải quyết. Tự động khóa đặt lịch khi đang bảo trì.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Thời khóa biểu trực quan</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Xem lịch theo kiểu thời khóa biểu trường học với 15 tiết × 7 ngày. Dễ nhìn, dễ kiểm tra.
                </p>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-blue-600">
        <div class="max-w-4xl mx-auto px-6 py-14 text-center">
            <h2 class="text-2xl font-bold text-white mb-3">Sẵn sàng bắt đầu?</h2>
            <p class="text-blue-100 mb-7">Đăng nhập bằng tài khoản được cấp hoặc tạo tài khoản mới.</p>
            <div class="flex flex-wrap gap-3 justify-center">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-flex items-center px-6 py-2.5 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                        Vào hệ thống
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-6 py-2.5 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                        Đăng nhập
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-6 py-2.5 bg-blue-500 text-white font-semibold rounded-lg border border-blue-400 hover:bg-blue-400 transition-colors">
                            Đăng ký tài khoản
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-gray-200 py-6 text-center text-sm text-gray-400">
        Lab Scheduler — Hệ thống quản lý phòng lab & thiết bị thực hành
    </footer>

</body>
</html>
