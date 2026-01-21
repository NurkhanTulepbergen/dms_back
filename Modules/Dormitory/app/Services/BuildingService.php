<?php

namespace Modules\Dormitory\Services;

use App\Exceptions\BusinessException;
use Modules\Dormitory\Models\Building;

class BuildingService
{
    public function getAll() {
        return Building::all();
    }

    public function getById($id) {
        return Building::findOrFail($id);
    }

    public function create(array $data) {
        return Building::create($data);
    }


    public function update(int $id, array $data): Building
    {
        $building = Building::findOrFail($id);

        if ($building->floors()->count() > $data['total_floors']) {
            throw new BusinessException(
                'Этаж не может быть меньше имеющегося',
                422
            );
        }

        $building->update($data);

        return $building;
    }


    public function delete(int $id) {
        $building = Building::findOrFail($id);

        $building->delete();
    }
}
