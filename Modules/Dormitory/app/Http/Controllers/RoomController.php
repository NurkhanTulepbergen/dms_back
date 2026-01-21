<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Services\RoomService;

class RoomController extends Controller
{
    public function __construct(

        private RoomService $roomService,
    )
    {}

    public function indexRoom()
    {
        $room = $this->roomService->getAll();

        return result($room, 200, 'Список комнат');
    }

    public function showRoom($id)
    {
        $room = $this->roomService->getById($id);

        return result($room, 200, 'Комната');
    }

    // ➕ POST /rooms
    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'floor_id'    => 'required|exists:floors,id',
            'room_number' => 'required|string|max:20',
            'capacity'    => 'required|integer|min:1|max:20',
            'live_cap'    => 'required|integer|min:0',
        ]);

        $room = $this->roomService->create($validated);

        return result($room, 201, 'Комната создана');
    }


    // ✏️ PUT /rooms/{id}
    public function updateRoom(Request $request, $id)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:20',
            'live_cap' => 'required|integer|min:0',
        ]);


        $room = $this->roomService->update($id, $validated);

        return result($room, 200, 'Комната успешно обновлена');
    }

    // 🗑 DELETE /rooms/{id}
    public function destroyRoom($id)
    {
        $this->roomService->delete($id);

        return response()->noContent();
    }

}
