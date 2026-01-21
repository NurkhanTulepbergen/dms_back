<?php


namespace Modules\Requests\Services;

use App\Exceptions\BusinessException;
use Modules\Dormitory\Models\Building;

class RequestChangeRoomService
{
    public function delete(int $id)
    {
        $building = Building::findOrFail($id);

        $building->delete();
    }
}
