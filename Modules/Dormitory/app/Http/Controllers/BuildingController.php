<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Services\BuildingService;

class BuildingController extends Controller
{
    public function __construct(
        private BuildingService $buildingService,
    )
    {}

    // GET /api/v1/buildings
    public function index()
    {
        $buildings = $this->buildingService->getAll();

        return result($buildings, 200, 'Список зданий');
    }

    // GET /api/v1/buildings/{building}
    public function show(Building $building)
    {
        return result($building, 200, 'Здание');
    }

    // POST /api/v1/buildings (role:admin,manager)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        $building = $this->buildingService->create($validated);

        return result($building, 201, 'Здание успешно создано');
    }

    // PUT/PATCH /api/v1/buildings/{building} (role:admin,manager)
    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        $building = $this->buildingService->update((int) $building->id, $validated);

        return result($building, 200, 'Данные успешно обновлены');
    }

    // DELETE /api/v1/buildings/{building} (role:admin,manager)
    public function destroy(Building $building)
    {
        $this->buildingService->delete((int) $building->id);
        return result(null, 204);
    }

}
