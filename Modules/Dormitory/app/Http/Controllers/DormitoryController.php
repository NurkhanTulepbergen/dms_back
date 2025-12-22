<?php

namespace Modules\Dormitory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;

class DormitoryController extends Controller
{
    // 📄 GET /buildings
    public function indexBuilding()
    {
        $buildings = Building::withCount('floors')->get();

        return response()->json([
            'data' => $buildings
        ]);
    }

    // 🔍 GET /buildings/{id}
    public function showBuilding($id)
    {
        $building = Building::with('floors')->findOrFail($id);

        return response()->json([
            'data' => $building
        ]);
    }

    // ➕ POST /buildings
    public function storeBuilding(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'message' => 'Building created successfully',
            'data' => $building
        ], 201);
    }

    // ✏️ PUT /buildings/{id}
    public function updateBuilding(Request $request, $id)
    {
        $building = Building::findOrFail($id);

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'total_floors' => 'required|integer|min:1|max:100',
        ]);

        // защита: нельзя уменьшить total_floors ниже существующих этажей
        if ($building->floors()->count() > $validated['total_floors']) {
            return response()->json([
                'error' => 'Total floors cannot be less than existing floors'
            ], 422);
        }

        $building->update($validated);

        return response()->json([
            'message' => 'Building updated successfully',
            'data' => $building
        ]);
    }

    // 🗑 DELETE /buildings/{id}
    public function destroyBuilding($id)
    {
        $building = Building::findOrFail($id);
        $building->delete();

        return response()->json([
            'message' => 'Building deleted successfully'
        ]);
    }

    // 📄 GET /floors
    public function indexFloor()
    {
        $floors = Floor::with('building')->get();

        return response()->json([
            'data' => $floors
        ]);
    }

    // 🔍 GET /floors/{id}
    public function showFloor($id)
    {
        $floor = Floor::with('building')->findOrFail($id);

        return response()->json([
            'data' => $floor
        ]);
    }

    // ➕ POST /floors
    public function storeFloor(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor_number' => 'required|integer|min:1',
        ]);

        $building = Building::findOrFail($validated['building_id']);

        // ❗ нельзя создать этаж выше total_floors
        if ($validated['floor_number'] > $building->total_floors) {
            return response()->json([
                'error' => 'Floor number exceeds total floors of the building'
            ], 422);
        }

        // ❗ защита от дублей
        if ($building->floors()
            ->where('floor_number', $validated['floor_number'])
            ->exists()) {
            return response()->json([
                'error' => 'This floor already exists in the building'
            ], 422);
        }

        $floor = Floor::create($validated);

        return response()->json([
            'message' => 'Floor created successfully',
            'data' => $floor
        ], 201);
    }

    // ✏️ PUT /floors/{id}
    public function updateFloor(Request $request, $id)
    {
        $floor = Floor::findOrFail($id);

        $validated = $request->validate([
            'floor_number' => 'required|integer|min:1',
        ]);

        $building = $floor->building;

        if ($validated['floor_number'] > $building->total_floors) {
            return response()->json([
                'error' => 'Floor number exceeds total floors of the building'
            ], 422);
        }

        // ❗ защита от дублей
        if ($building->floors()
            ->where('floor_number', $validated['floor_number'])
            ->where('id', '!=', $floor->id)
            ->exists()) {
            return response()->json([
                'error' => 'This floor already exists in the building'
            ], 422);
        }

        $floor->update($validated);

        return response()->json([
            'message' => 'Floor updated successfully',
            'data' => $floor
        ]);
    }

    // 🗑 DELETE /floors/{id}
    public function destroyFloor($id)
    {
        $floor = Floor::findOrFail($id);
        $floor->delete();

        return response()->json([
            'message' => 'Floor deleted successfully'
        ]);
    }


    // 📄 GET /rooms
    public function indexRooms()
    {
        $rooms = Room::with('floor.building')->get();

        return response()->json([
            'data' => $rooms
        ]);
    }

    // 🔍 GET /rooms/{id}
    public function showRooms($id)
    {
        $room = Room::with('floor.building')->findOrFail($id);

        return response()->json([
            'data' => $room
        ]);
    }

    // ➕ POST /rooms
    public function storeRooms(Request $request)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:20',
            'live_cap' => 'required|integer|min:0',
        ]);

        $floor = Floor::findOrFail($validated['floor_id']);

        // ❗ нельзя превышать вместимость
        if ($validated['live_cap'] > $validated['capacity']) {
            return response()->json([
                'error' => 'live_cap cannot be greater than capacity'
            ], 422);
        }

        // ❗ защита от дублей комнат на одном этаже
        if ($floor->rooms()
            ->where('room_number', $validated['room_number'])
            ->exists()) {
            return response()->json([
                'error' => 'This room already exists on this floor'
            ], 422);
        }

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    // ✏️ PUT /rooms/{id}
    public function updateRooms(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'room_number' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:20',
            'live_cap' => 'required|integer|min:0',
        ]);

        if ($validated['live_cap'] > $validated['capacity']) {
            return response()->json([
                'error' => 'live_cap cannot be greater than capacity'
            ], 422);
        }

        // ❗ защита от дублей
        if ($room->floor->rooms()
            ->where('room_number', $validated['room_number'])
            ->where('id', '!=', $room->id)
            ->exists()) {
            return response()->json([
                'error' => 'This room already exists on this floor'
            ], 422);
        }

        $room->update($validated);

        return response()->json([
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    // 🗑 DELETE /rooms/{id}
    public function destroyRooms($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully'
        ]);
    }

}
