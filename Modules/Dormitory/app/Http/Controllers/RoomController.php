<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Models\Room;
use Modules\Dormitory\Services\RoomService;

class RoomController extends Controller
{
    public function __construct(

        private RoomService $roomService,
    )
    {}

    // GET /api/v1/rooms
    public function index()
    {
        $rooms = $this->roomService->getAll();

        return result($rooms, 200, 'Список комнат');
    }

    // GET /api/v1/rooms/{room}
    public function show(Room $room)
    {
        $room = $this->roomService->getById((int) $room->id);

        return result($room, 200, 'Комната');
    }

    // POST /api/v1/rooms (role:admin,manager)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_id'    => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:20',
            'capacity'    => 'required|integer|min:1|max:20',
            'live_cap'    => 'required|integer|min:0',
        ]);

        $room = $this->roomService->create($validated);

        return result($room, 201, 'Комната создана');
    }


    // PUT/PATCH /api/v1/rooms/{room} (role:admin,manager)
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:20',
            'live_cap' => 'required|integer|min:0',
        ]);


        $room = $this->roomService->update((int) $room->id, $validated);

        return result($room, 200, 'Комната успешно обновлена');
    }

    // DELETE /api/v1/rooms/{room} (role:admin,manager)
    public function destroy(Room $room)
    {
        $this->roomService->delete((int) $room->id);
        return result(null, 204);
    }

}
