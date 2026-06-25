<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\Room;
use App\Models\User;
use App\Notifications\MaintenanceReportedNotification;
use App\Notifications\MaintenanceStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AdminMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $logs = MaintenanceLog::with(['loggable', 'reporter'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.maintenance.index', compact('logs'));
    }

    public function show(MaintenanceLog $log): View
    {
        $log->load(['loggable', 'reporter']);

        return view('admin.maintenance.show', compact('log'));
    }

    public function create(): View
    {
        $rooms     = Room::orderBy('name')->get();
        $equipment = Equipment::with('category')->orderBy('name')->get();

        return view('admin.maintenance.create', compact('rooms', 'equipment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loggable_type' => 'required|in:App\Models\Room,App\Models\Equipment',
            'loggable_id'   => 'required|integer|min:1',
            'description'   => 'required|string|max:1000',
        ]);

        $log = MaintenanceLog::create(array_merge($validated, [
            'reported_by' => $request->user()->id,
            'status'      => 'open',
        ]));

        Notification::send(User::role('admin')->get(), new MaintenanceReportedNotification($log));

        return redirect()->route('admin.maintenance.index')->with('success', 'Đã báo cáo sự cố thành công.');
    }

    public function progress(Request $request, MaintenanceLog $log): RedirectResponse
    {
        $log->load('loggable');
        $log->update(['status' => 'in_progress']);

        $this->notifyStatusChange($log);

        return back()->with('success', 'Đã chuyển sang trạng thái đang xử lý.');
    }

    public function resolve(Request $request, MaintenanceLog $log): RedirectResponse
    {
        $log->load('loggable');
        $log->update(['status' => 'resolved', 'resolved_at' => now()]);

        $this->notifyStatusChange($log);

        return back()->with('success', 'Đã đánh dấu sự cố là đã giải quyết.');
    }

    private function notifyStatusChange(MaintenanceLog $log): void
    {
        Notification::send(User::role('admin')->get(), new MaintenanceStatusChangedNotification($log));
    }
}
