<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function stream(Request $request): StreamedResponse
    {
        $userId = $request->user()->id;
        session()->save(); // release session lock before long-running stream

        return response()->stream(function () use ($userId) {
            set_time_limit(0);

            while (true) {
                if (connection_aborted()) break;

                try {
                    $user  = User::find($userId);
                    if (!$user) break;

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

                    echo 'data: ' . json_encode(['count' => $count, 'items' => $items]) . "\n\n";

                    if (ob_get_level() > 0) ob_flush();
                    flush();

                    \DB::disconnect(); // release DB connection during sleep
                } catch (\Throwable) {
                    break; // client will auto-reconnect via EventSource
                }

                sleep(10);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
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
