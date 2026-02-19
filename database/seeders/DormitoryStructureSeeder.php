<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;

class DormitoryStructureSeeder extends Seeder
{
    /**
     * Seed 2 buildings, each with 6 floors and 4 rooms per floor.
     */
    public function run(): void
    {
        for ($buildingIndex = 1; $buildingIndex <= 2; $buildingIndex++) {
            $building = Building::updateOrCreate(
                ['address' => "Общежитие {$buildingIndex}"],
                ['total_floors' => 6]
            );

            for ($floorNumber = 1; $floorNumber <= 6; $floorNumber++) {
                $floor = Floor::updateOrCreate(
                    [
                        'building_id' => $building->id,
                        'floor_number' => $floorNumber,
                    ],
                    [
                        'gender_policy' => 'mixed',
                        'is_active' => true,
                    ]
                );

                for ($roomOffset = 1; $roomOffset <= 4; $roomOffset++) {
                    $roomNumber = ($floorNumber * 100) + $roomOffset;

                    Room::updateOrCreate(
                        [
                            'floor_id' => $floor->id,
                            'room_number' => $roomNumber,
                        ],
                        [
                            'capacity' => 3,
                            'live_cap' => 0,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
