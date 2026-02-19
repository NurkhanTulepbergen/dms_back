<?php

namespace Modules\Dormitory\Services;

use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Models\RoomType;

class RoomService
{
    public function getAll() {
        return Room::all();
    }

    public function getById($id) {
        return Room::findOrFail($id);
    }

    public function create(array $data): Room
    {
        if (!isset($data['room_type_id'])) {
            throw new BusinessException('room_type_id is required', 422);
        }

        RoomType::findOrFail((int) $data['room_type_id']);

        // ❗ нельзя превышать вместимость
        if ($data['live_cap'] > $data['capacity']) {
            throw new BusinessException(
                'live_cap cannot be greater than capacity',
                422
            );
        }

        // получаем этаж
        $floor = Floor::findOrFail($data['floor_id']);

        // ❗ защита от дублей комнат на одном этаже
        $exists = $floor->rooms()
            ->where('room_number', $data['room_number'])
            ->exists();

        if ($exists) {
            throw new BusinessException(
                'This room already exists on this floor',
                422
            );
        }

        return Room::create($data);
    }

    public function update($id, array $data): Room
    {
        $room = Room::findOrFail($id);

        if (!isset($data['room_type_id'])) {
            throw new BusinessException('room_type_id is required', 422);
        }

        RoomType::findOrFail((int) $data['room_type_id']);

        if ($data['live_cap'] > $data['capacity']) {
            throw new BusinessException(
                'Число проживающих не может превышать вместимость комнаты',
                422
            );
        }

        if ($room->floor->rooms()
            ->where('room_number', $data['room_number'])
            ->where('id', '!=', $room->id)
            ->exists()) {
            throw new BusinessException(
                'Эта комната уже существует в этом этаже',
                422
            );
        }

        $room->update($data);

        return $room;
    }

    public function delete($id)
    {
        $room = Room::findOrFail($id);

        $room->delete();
    }

    public function getAllRoomsForFloor($id)
    {
        $rooms = Room::where('floor_id', $id)->get();

        if($rooms->isEmpty()) {
            throw new ModelNotFoundException();
        }
        return $rooms;
    }
}
