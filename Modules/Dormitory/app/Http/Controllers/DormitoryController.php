<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Services\FloorService;
use Modules\Dormitory\Services\RoomService;

class DormitoryController extends Controller
{
    public function __construct(
        private FloorService $floorService,
        private RoomService $roomService
    )
    {}

    // GET /api/v1/buildings/{building}/floors
    public function getFloorsForBuilding(Building $building)
    {
        $floors = $this->floorService->getFloorsForBuilding((int) $building->id);

        return result($floors, 200, 'Этажи');
    }

    // GET /api/v1/floors/{floor}/rooms
    public function getRoomsForFloor(Floor $floor)
    {
        $rooms = $this->roomService->getAllRoomsForFloor((int) $floor->id);

        return result($rooms, 200, 'Комнаты');
    }
}

