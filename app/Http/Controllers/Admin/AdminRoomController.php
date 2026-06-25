<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminRoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::withCount('bookings')->orderBy('name')->paginate(20);

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'building'    => 'nullable|string|max:100',
            'capacity'    => 'required|integer|min:1',
            'type'        => 'required|in:lab,classroom,workshop',
            'status'      => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Đã tạo phòng thành công.');
    }

    public function show(Room $room): View
    {
        $room->load(['bookings' => fn ($q) => $q->latest()->limit(10)->with('user'), 'maintenanceLogs' => fn ($q) => $q->latest()->limit(5)]);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'building'    => 'nullable|string|max:100',
            'capacity'    => 'required|integer|min:1',
            'type'        => 'required|in:lab,classroom,workshop',
            'status'      => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Đã cập nhật phòng thành công.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Đã xoá phòng thành công.');
    }
}
