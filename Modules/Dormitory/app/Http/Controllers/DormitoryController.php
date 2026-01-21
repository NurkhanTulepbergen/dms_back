<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dormitory\Services\FloorService;
use Modules\Dormitory\Services\RoomService;

class DormitoryController extends Controller
{
    public function __construct(
        private FloorService $floorService,
        private RoomService $roomService
    )
    {}
    public function getFloorsForBuilding($id)
    {
        $floors = $this->floorService->getFloorsForBuilding($id);

        return result($floors, 200, 'Этажи');
    }

    public function getRoomsForFloor($id)
    {
        $rooms = $this->roomService->getAllRoomsForFloor($id);

        return result($rooms, 200, 'Комнаты');
    }
}


