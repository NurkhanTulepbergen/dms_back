<?php


namespace Modules\Requests\Services;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Room;
use Modules\Requests\Models\RequestLive;

class RequestLiveService
{
    public function pendingLiveRequest($data, $user)
    {

        // защита: одна активная заявка
        if (RequestLive::where('user_id', $user->id)
            ->whereIn('status', ['pending','accepted'])
            ->exists()) {
            throw new BusinessException(
                'Вы уже отправили запрос на проживание',
                422
            );
        }

        $room = Room::findOrFail($data['room_id']);


        // нет мест
        if ($room->live_cap >= $room->capacity) {
            throw new BusinessException(
                'Нету свободных мест в комнате',
                422
            );
        }

        $requestLive = RequestLive::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'documents' => $data['documents'] ?? null,
            'status' => 'pending',
        ]);

        return $requestLive;
    }

    public function approveByManager($id)
    {
        $requestLive = RequestLive::findOrFail($id);

        if ($requestLive->status !== 'pending') {
            throw new BusinessException(
                'Запрос уже обработан',
                422
            );
        }

        $room = $requestLive->room;

        if ($room->live_cap >= $room->capacity) {
            throw new BusinessException(
                'Нету свободных мест в комнате',
                422
            );
        }

        $requestLive->update([
            'status' => 'accepted'
        ]);

        $room->increment('live_cap');

        return $requestLive;
    }

    public function rejectByManager($id)
    {
        $requestLive = RequestLive::findOrFail($id);

        if ($requestLive->status !== 'pending') {
            throw new BusinessException(
                'Запрос уже обработан',
                422
            );
        }

        $requestLive->update([
            'status' => 'rejected'
        ]);

        return $requestLive;
    }

}
