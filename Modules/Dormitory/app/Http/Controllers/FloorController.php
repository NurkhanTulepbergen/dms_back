<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Services\FloorService;

class FloorController extends Controller
{
    public function __construct(
        private FloorService $floorService,
    )
    {}

    // GET /api/v1/floors
    public function index()
    {
        $floors = $this->floorService->getAll();

        return result($floors, 200, "Список этажей");
    }

    // GET /api/v1/floors/{floor}
    public function show(Floor $floor)
    {
        $floor = $this->floorService->getById((int) $floor->id);

        return result($floor, 200, 'Этаж');
    }

    // POST /api/v1/floors (role:admin,manager)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|integer|min:1',
        ]);

        $floor = $this->floorService->create($validated);

        return result($floor, 201, 'Этаж создан успещно');
    }

    // PUT/PATCH /api/v1/floors/{floor} (role:admin,manager)
    public function update(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer|min:1',
        ]);

        $floor = $this->floorService->update((int) $floor->id, $validated);

        return result($floor, 200, 'Этаж успешно обновлен');
    }

    // DELETE /api/v1/floors/{floor} (role:admin,manager)
    public function destroy(Floor $floor)
    {
        $this->floorService->delete((int) $floor->id);
        return result(null, 204);
    }
}
