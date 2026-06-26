<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:500']);

        $user    = $request->user();
        $history = $request->input('history', []);

        // Build live context from DB
        $availableRooms  = Room::available()->get(['name', 'building', 'capacity']);
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        $maintenanceEquip = Equipment::where('status', 'maintenance')->count();
        $pendingBookings  = Booking::forUser($user->id)->pending()->count();
        $approvedBookings = Booking::forUser($user->id)->approved()->count();

        $roomList = $availableRooms->isEmpty()
            ? '(không có phòng trống hiện tại)'
            : $availableRooms->map(fn ($r) => "- {$r->name} (tòa {$r->building}, {$r->capacity} chỗ)")->join("\n");

        $role = $user->roles->first()?->name ?? 'user';

        $systemPrompt = <<<EOT
Bạn là trợ lý AI của hệ thống Lab Scheduler — quản lý phòng lab và thiết bị thực hành tại trường học.
Người dùng hiện tại: {$user->name} (vai trò: {$role})

Dữ liệu hệ thống lúc này:
- Phòng trống: {$availableRooms->count()} phòng, đang bảo trì: {$maintenanceRooms} phòng
- Thiết bị đang bảo trì: {$maintenanceEquip}
- Booking của {$user->name}: {$pendingBookings} chờ duyệt, {$approvedBookings} đã duyệt

Danh sách phòng đang trống:
{$roomList}

Hướng dẫn:
- Trả lời ngắn gọn bằng tiếng Việt, không dùng markdown thừa
- Chỉ trả lời về hệ thống đặt phòng/thiết bị/booking/bảo trì
- Nếu câu hỏi ngoài phạm vi, lịch sự từ chối
EOT;

        // Build messages for API: system + conversation history
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (array_slice($history, -8) as $h) {
            $r = $h['role'] ?? '';
            $c = $h['content'] ?? '';
            if (in_array($r, ['user', 'assistant']) && is_string($c) && $c !== '') {
                $messages[] = ['role' => $r, 'content' => $c];
            }
        }

        try {
            $response = Http::timeout(20)
                ->withToken(config('services.groq.key'))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => config('services.groq.model'),
                    'messages'    => $messages,
                    'max_tokens'  => 400,
                    'temperature' => 0.6,
                ]);

            $reply = $response->json('choices.0.message.content')
                ?? 'Xin lỗi, tôi không thể trả lời lúc này.';
        } catch (\Throwable) {
            $reply = 'Lỗi kết nối AI. Vui lòng thử lại sau.';
        }

        return response()->json(['reply' => trim($reply)]);
    }
}
