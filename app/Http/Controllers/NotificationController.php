<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unreadCount(Request $request): JsonResponse
    {
        $user  = $request->user();
        $count = $user->unreadNotifications()->count();
        $items = $user->unreadNotifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($n) => [
                'id'      => $n->id,
                'message' => $n->data['message'] ?? '',
                'time'    => $n->created_at->diffForHumans(),
            ]);

        return response()->json(['count' => $count, 'items' => $items]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
