<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Models\RoomType;

class DormitoryStructureSeeder extends Seeder
{
    /**
     * Seed predefined buildings/floors/rooms/room types.
     */
    public function run(): void
    {
        $roomTypes = [
            ['name' => 'Business', 'semester_price' => 1000000],
            ['name' => 'Comfort+', 'semester_price' => 800000],
            ['name' => 'Comfort', 'semester_price' => 700000],
            ['name' => 'Econom', 'semester_price' => 500000],
        ];

        $roomTypeIds = [];
        foreach ($roomTypes as $roomTypeData) {
            $roomType = RoomType::updateOrCreate(
                ['name' => $roomTypeData['name']],
                ['semester_price' => $roomTypeData['semester_price']]
            );
            $roomTypeIds[] = $roomType->id;
        }

        $buildings = [
            ['address' => 'Ислама Каримова, 70 к1', 'total_floors' => 7, 'gender_policy' => 'mixed'],
            ['address' => 'Ислама Каримова, 70 к2', 'total_floors' => 6, 'gender_policy' => 'female'],
            ['address' => 'Ислама Каримова, 70 к3', 'total_floors' => 5, 'gender_policy' => 'male'],
        ];

        foreach ($buildings as $buildingData) {
            $building = Building::updateOrCreate(
                ['address' => $buildingData['address']],
                ['total_floors' => $buildingData['total_floors']]
            );

            for ($floorNumber = 1; $floorNumber <= $buildingData['total_floors']; $floorNumber++) {
                $floor = Floor::updateOrCreate(
                    [
                        'building_id' => $building->id,
                        'floor_number' => $floorNumber,
                    ],
                    [
                        'gender_policy' => $buildingData['gender_policy'],
                        'is_active' => true,
                    ]
                );

                for ($roomOffset = 1; $roomOffset <= 5; $roomOffset++) {
                    $roomNumber = ($floorNumber * 100) + $roomOffset;
                    $roomTypeId = $roomTypeIds[($roomOffset - 1) % count($roomTypeIds)];

                    Room::updateOrCreate(
                        [
                            'floor_id' => $floor->id,
                            'room_number' => $roomNumber,
                        ],
                        [
                            'room_type_id' => $roomTypeId,
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
