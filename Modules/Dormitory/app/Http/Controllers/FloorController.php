<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Services\FloorService;

class FloorController extends Controller
{
    public function __construct(
        private FloorService $floorService,
    )
    {}

    public function indexFloor()
    {
        $building = $this->floorService->getAll();

        return result($building, 200, "Список этажей");
    }

    public function showFloor($id)
    {
        $floor = $this->floorService->getById($id);

        return result($floor, 200, 'Этаж');
    }

    public function storeFloor(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|integer|min:1',
        ]);

        $floor = $this->floorService->create($validated);

        return result($floor, 201, 'Этаж создан успещно');
    }

    public function updateFloor(Request $request, $id)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer|min:1',
        ]);

        $floor = $this->floorService->update($id, $validated);

        return result($floor, 200, 'Этаж успешно обновлен');
    }

    public function destroyFloor($id)
    {
        $this->floorService->delete($id);

        return response()->noContent();
    }
}
