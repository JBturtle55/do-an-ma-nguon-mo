<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $equipment = Equipment::with('category', 'room')
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('name')
            ->paginate(16)
            ->withQueryString();

        $categories = EquipmentCategory::orderBy('name')->get();

        return view('equipment.index', compact('equipment', 'categories'));
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load(['category', 'room']);
        $recentBookings = $equipment->bookings()
            ->with('user')
            ->approved()
            ->where('end_time', '>=', now()->subDays(7))
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('equipment.show', compact('equipment', 'recentBookings'));
    }
}
