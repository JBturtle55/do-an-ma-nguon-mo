<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(protected AvailabilityService $availabilityService) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'bookable_type' => 'required|string|in:App\Models\Room,App\Models\Equipment',
            'bookable_id'   => 'required|integer|min:1',
            'start_time'    => 'required|date',
            'end_time'      => 'required|date|after:start_time',
            'exclude_id'    => 'nullable|integer',
        ]);

        $start = Carbon::parse($request->start_time);
        $end   = Carbon::parse($request->end_time);

        // Check room/equipment status before checking booking conflicts
        if (!$this->availabilityService->isStatusBookable($request->bookable_type, $request->bookable_id)) {
            return response()->json([
                'available'   => false,
                'maintenance' => true,
                'conflicts'   => [],
            ]);
        }

        $conflicts = $this->availabilityService->getConflicts(
            $request->bookable_type,
            $request->bookable_id,
            $start,
            $end
        );

        if ($request->filled('exclude_id')) {
            $conflicts = $conflicts->where('id', '!=', (int) $request->exclude_id)->values();
        }

        return response()->json([
            'available'   => $conflicts->isEmpty(),
            'maintenance' => false,
            'conflicts'   => $conflicts->map(fn ($b) => [
                'id'         => $b->id,
                'title'      => $b->title,
                'start_time' => $b->start_time->toIso8601String(),
                'end_time'   => $b->end_time->toIso8601String(),
                'status'     => $b->status,
            ]),
        ]);
    }
}
