<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = Schedule::with('room')->orderBy('room_id')->orderBy('day_of_week')->paginate(20);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $rooms = Room::orderBy('name')->get();

        return view('admin.schedules.create', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'title'          => 'nullable|string|max:255',
            'recurring_type' => 'required|in:none,daily,weekly',
            'day_of_week'    => 'nullable|integer|min:0|max:6',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Đã tạo lịch cố định thành công.');
    }

    public function edit(Schedule $schedule): View
    {
        $rooms = Room::orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'rooms'));
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'title'          => 'nullable|string|max:255',
            'recurring_type' => 'required|in:none,daily,weekly',
            'day_of_week'    => 'nullable|integer|min:0|max:6',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')->with('success', 'Đã cập nhật lịch thành công.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Đã xoá lịch thành công.');
    }
}
