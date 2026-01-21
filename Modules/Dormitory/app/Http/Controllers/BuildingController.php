<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Services\BuildingService;

class BuildingController extends Controller
{
    public function __construct(
        private BuildingService $buildingService,
    )
    {}

    public function indexBuilding()
    {
        $buildings = $this->buildingService->getAll();

        return result($buildings, 200, 'Список зданий');
    }

    public function showBuilding($id)
    {
        $building = $this->buildingService->getById($id);

        return result($building, 200, 'Здание');
    }

    public function storeBuilding(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        $building = $this->buildingService->create($validated);

        return result($building, 201, 'Здание успешно создано');
    }

    public function updateBuilding(Request $request, int $id)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        $building = $this->buildingService->update($id, $validated);

        return result($building, 200, 'Данные успешно обновлены');
    }



    public function destroyBuilding(int $id)
    {

        $this->buildingService->delete($id);

        return response()->noContent();
    }

}


