<?php

namespace Modules\Requests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Requests\Models\RequestChangeRoom;
use Modules\Requests\Services\RequestChangeRoomService;

class RequestChangeRoomController extends Controller
{
    public function __construct(
        private readonly RequestChangeRoomService $requestChangeRoomService,
    ) {}

    /**
     * 👨‍🎓 Студент подаёт заявку на смену комнаты
     * POST /requests/change-room
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'preferred_room_id' => 'nullable|exists:rooms,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $roomId = (int) ($validated['room_id'] ?? $validated['preferred_room_id'] ?? 0);

        if ($roomId <= 0) {
            throw ValidationException::withMessages([
                'room_id' => ['The room id field is required.'],
            ]);
        }

        $user = Auth::user();

        $requestChange = $this->requestChangeRoomService->createRequest(
            (int) $user->id,
            $roomId,
            $validated['description'] ?? null,
        );

        return result($requestChange, 201, 'Заявка на смену комнаты отправлена');
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

        return result($requests, 200, 'Заявки на смену комнаты');
    }

    /**
     * 👨‍💼 Менеджер: принять заявку
     * POST /requests/change-room/{id}/approve
     */
    public function approve($id)
    {
        $result = $this->requestChangeRoomService->approve((int) $id);

        return result($result, 200, 'Заявка на смену комнаты принята');
    }

    /**
     * 👨‍💼 Менеджер: отклонить заявку
     * POST /requests/change-room/{id}/reject
     */
    public function reject($id)
    {
        $requestChange = $this->requestChangeRoomService->reject((int) $id);

        return result($requestChange, 200, 'Заявка на смену комнаты отклонена');
    }
}
