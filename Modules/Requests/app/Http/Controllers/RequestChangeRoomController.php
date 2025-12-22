<?php

namespace Modules\Requests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Requests\Models\RequestChangeRoom;
use Modules\Dormitory\Models\Room;
use Modules\User\Models\DormStudent;

class RequestChangeRoomController extends Controller
{
    /**
     * 👨‍🎓 Студент подаёт заявку на смену комнаты
     * POST /requests/change-room
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $dormStudent = DormStudent::where('user_id', $user->id)->firstOrFail();

        // ❗ нельзя подавать, если уже есть активная заявка
        if (RequestChangeRoom::where('student_id', $dormStudent->user_id)
            ->where('status', 'pending')
            ->exists()) {
            return response()->json([
                'error' => 'You already have a pending change room request'
            ], 422);
        }

        $newRoom = Room::findOrFail($validated['room_id']);

        // ❗ нет свободных мест
        if ($newRoom->live_cap >= $newRoom->capacity) {
            return response()->json([
                'error' => 'No free places in selected room'
            ], 422);
        }

        $requestChange = RequestChangeRoom::create([
            'student_id' => $dormStudent->user_id,
            'room_id' => $newRoom->id,
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Change room request submitted',
            'data' => $requestChange
        ], 201);
    }

    /**
     * 👨‍💼 Менеджер: список заявок
     * GET /requests/change-room
     */
    public function index()
    {
        $requests = RequestChangeRoom::with([
            'student.user',
            'room.floor.building'
        ])->get();

        return response()->json([
            'data' => $requests
        ]);
    }

    /**
     * 👨‍💼 Менеджер: принять заявку
     * POST /requests/change-room/{id}/approve
     */
    public function approve($id)
    {
        $requestChange = RequestChangeRoom::findOrFail($id);

        if ($requestChange->status !== 'pending') {
            return response()->json([
                'error' => 'Request already processed'
            ], 422);
        }

        $newRoom = $requestChange->room;

        if ($newRoom->live_cap >= $newRoom->capacity) {
            return response()->json([
                'error' => 'No free places in selected room'
            ], 422);
        }

        DB::transaction(function () use ($requestChange, $newRoom) {
            $student = DormStudent::where('user_id', $requestChange->student_id)->firstOrFail();

            // уменьшить занятость старой комнаты
            if ($student->room_id) {
                Room::where('id', $student->room_id)->decrement('live_cap');
            }

            // увеличить занятость новой комнаты
            $newRoom->increment('live_cap');

            // обновить студента
            $student->update([
                'room_id' => $newRoom->id
            ]);

            // обновить заявку
            $requestChange->update([
                'status' => 'accepted'
            ]);
        });

        return response()->json([
            'message' => 'Change room request approved'
        ]);
    }

    /**
     * 👨‍💼 Менеджер: отклонить заявку
     * POST /requests/change-room/{id}/reject
     */
    public function reject($id)
    {
        $requestChange = RequestChangeRoom::findOrFail($id);

        if ($requestChange->status !== 'pending') {
            return response()->json([
                'error' => 'Request already processed'
            ], 422);
        }

        $requestChange->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Change room request rejected'
        ]);
    }
}
