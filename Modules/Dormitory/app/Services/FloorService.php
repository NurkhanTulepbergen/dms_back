<?php

namespace Modules\Dormitory\Services;

use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;


class FloorService
{
    public function getAll() {
        return Floor::all();
    }

    public function getById($id) {
        $floor = Floor::with('building')->findOrFail($id);

        return $floor;
    }

    public function create($data) {
        $building = Building::findOrFail($data['building_id']);

        if($data['floor_number'] > $building->total_floors) {
            throw new BusinessException(
                'Этаж не может быть больше максимального этажа здания',
                422
            );
        }


        if ($building->floors()->where('floor_number', $data['floor_number'])
            ->exists()) {
            throw new BusinessException(
                'Данный этаж уже существует',
                422
            );
        }

        return Floor::create($data);
    }

    public function update($id, $data) {
        $floor = Floor::findOrFail($id);
        $building = $floor->building;

        if($data['floor_number'] > $building->total_floors) {
            throw new BusinessException(
                'Этаж не может быть больше максимального этажа здания',
                422
            );
        }

        if ($building->floors()
                ->where('floor_number', $data['floor_number'])
                ->where('id', '!=', $floor->id)
                ->exists()
        ) {
            throw new BusinessException(
                'Данный этаж уже существует',
                422
            );
        }


        $floor->update($data);

        return $floor;
    }

    public function delete($id) {
        $floor = Floor::findOrFail($id);

        return $floor->delete();
    }

    public function getFloorsForBuilding($id)
    {
        $floors = Floor::where('building_id', $id)->get();

        if($floors->isEmpty()) {
            throw new ModelNotFoundException();
        }

        return $floors;
    }
}
