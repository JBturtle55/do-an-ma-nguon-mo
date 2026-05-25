<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEquipmentController extends Controller
{
    public function index(): View
    {
        $equipment = Equipment::with(['category', 'room'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.equipment.index', compact('equipment'));
    }

    public function create(): View
    {
        $categories = EquipmentCategory::orderBy('name')->get();
        $rooms      = Room::orderBy('name')->get();

        return view('admin.equipment.create', compact('categories', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'room_id'     => 'nullable|exists:rooms,id',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
        ]);

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')->with('success', 'Đã tạo thiết bị thành công.');
    }

    public function edit(Equipment $equipment): View
    {
        $categories = EquipmentCategory::orderBy('name')->get();
        $rooms      = Room::orderBy('name')->get();

        return view('admin.equipment.edit', compact('equipment', 'categories', 'rooms'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'room_id'     => 'nullable|exists:rooms,id',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
        ]);

        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')->with('success', 'Đã cập nhật thiết bị thành công.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Đã xoá thiết bị thành công.');
    }
}
