<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white transform transition-transform duration-200 lg:relative lg:translate-x-0 lg:block flex-shrink-0">
    <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-700">
        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-sm leading-tight">Lab Scheduler</div>
            <div class="text-gray-400 text-xs">Quản lý phòng & thiết bị</div>
        </div>
    </div>

    <nav class="px-3 py-4 space-y-1 overflow-y-auto h-full pb-20">
        @php $role = auth()->user()->roles->first()?->name; @endphp

        {{-- Common links --}}
        @if($role === 'admin')
        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Admin Dashboard
        </x-nav-link>
        @else
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </x-nav-link>
        @endif

        @if($role !== 'admin')
        <div class="pt-3 pb-1">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Đặt lịch</p>
        </div>
        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index', 'bookings.show')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Lịch đặt của tôi
        </x-nav-link>
        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Đặt lịch mới
        </x-nav-link>
        @endif

        @if($role !== 'admin')
        <div class="pt-3 pb-1">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tra cứu</p>
        </div>
        <x-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 21h6"/></svg>
            Phòng lab
        </x-nav-link>
        <x-nav-link :href="route('equipment.index')" :active="request()->routeIs('equipment.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/></svg>
            Thiết bị
        </x-nav-link>
        @endif

        @if($role === 'admin')
        <div class="pt-3 pb-1">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quản trị</p>
        </div>
        <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Duyệt Booking
        </x-nav-link>
        <x-nav-link :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 21h6M3 21h18"/></svg>
            Quản lý Phòng
        </x-nav-link>
        <x-nav-link :href="route('admin.equipment.index')" :active="request()->routeIs('admin.equipment.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            Quản lý Thiết bị
        </x-nav-link>
        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Quản lý Người dùng
        </x-nav-link>
        <x-nav-link :href="route('admin.maintenance.index')" :active="request()->routeIs('admin.maintenance.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Bảo trì / Sự cố
        </x-nav-link>
        <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Báo cáo thống kê
        </x-nav-link>
        @endif
    </nav>
</aside>
