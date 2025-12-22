<?php

namespace Modules\Requests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Requests\Models\RequestLive;
use Modules\Dormitory\Models\Room;
use Illuminate\Support\Facades\Auth;

class RequestLiveController extends Controller
{
    /**
     * 👨‍🎓 Студент подаёт заявку на проживание
     * POST /requests/live
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'documents' => 'nullable|array',
        ]);

        $user = Auth::user();

        // ❗ защита: одна активная заявка
        if (RequestLive::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists()) {
            return response()->json([
                'error' => 'You already have a pending request'
            ], 422);
        }

        $room = Room::findOrFail($validated['room_id']);

        // ❗ нет мест
        if ($room->live_cap >= $room->capacity) {
            return response()->json([
                'error' => 'No free places in this room'
            ], 422);
        }

        $requestLive = RequestLive::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'documents' => $validated['documents'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Request submitted successfully',
            'data' => $requestLive
        ], 201);
    }

    /**
     * 👨‍💼 Менеджер: список заявок
     * GET /requests/live
     */
    public function index()
    {
        $requests = RequestLive::with(['student', 'room.floor.building'])->get();

        return response()->json([
            'data' => $requests
        ]);
    }

    /**
     * 👨‍💼 Менеджер: принять заявку
     * POST /requests/live/{id}/approve
     */
    public function approve($id)
    {
        $requestLive = RequestLive::findOrFail($id);

        if ($requestLive->status !== 'pending') {
            return response()->json([
                'error' => 'Request already processed'
            ], 422);
        }

        $room = $requestLive->room;

        if ($room->live_cap >= $room->capacity) {
            return response()->json([
                'error' => 'No free places in this room'
            ], 422);
        }

        // обновляем заявку
        $requestLive->update([
            'status' => 'accepted'
        ]);

        // увеличиваем занятость комнаты
        $room->increment('live_cap');

        return response()->json([
            'message' => 'Request approved'
        ]);
    }

    /**
     * 👨‍💼 Менеджер: отклонить заявку
     * POST /requests/live/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $requestLive = RequestLive::findOrFail($id);

        if ($requestLive->status !== 'pending') {
            return response()->json([
                'error' => 'Request already processed'
            ], 422);
        }

        $requestLive->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Request rejected'
        ]);
    }
}
