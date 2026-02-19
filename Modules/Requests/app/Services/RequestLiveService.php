<?php


namespace Modules\Requests\Services;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Room;
use Modules\Requests\Models\Document;
use Modules\Requests\Models\RequestLive;
use Modules\Settlement\Models\Settlement;
use Modules\Settlement\Services\SettlementService;

class RequestLiveService
{
    public function __construct(
        private readonly SettlementService $settlementService,
    ) {}

    public function pendingLiveRequest($data, $user)
    {
        // If already living (active settlement) -> cannot submit.
        $alreadyLiving = Settlement::query()
            ->where('user_id', $user->id)
            ->whereNull('end_at')
            ->exists();

        if ($alreadyLiving) {
            throw new BusinessException('У вас уже есть активное заселение', 422);
        }

        // защита: одна активная заявка
        if (RequestLive::where('user_id', $user->id)
            ->whereIn('status', ['pending','accepted'])
            ->exists()) {
            throw new BusinessException(
                'Вы уже отправили запрос на проживание',
                422
            );
        }

        $roomId = (int) $data['preferred_room_id'];
        $room = Room::findOrFail($roomId);


        // нет мест
        $activeOccupancy = Settlement::query()
            ->where('room_id', $room->id)
            ->whereNull('end_at')
            ->count();
        if ($activeOccupancy >= $room->capacity) {
            throw new BusinessException(
                'Нету свободных мест в комнате',
                422
            );
        }

        $requestLive = RequestLive::create([
            'user_id' => $user->id,
            'preferred_room_id' => $room->id,
            'status' => 'pending',
        ]);

        if (!empty($data['documents']) && is_array($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                if (!is_array($doc)) {
                    continue;
                }
                $type = $doc['type'] ?? null;
                $path = $doc['path'] ?? null;
                if (!is_string($type) || $type === '' || !is_string($path) || $path === '') {
                    continue;
                }
                Document::create([
                    'request_id' => $requestLive->id,
                    'type' => $type,
                    'path' => $path,
                ]);
            }
        }

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

        if ($requestLive->preferred_room_id === null) {
            throw new BusinessException('Preferred room is not set for this request', 422);
        }

        // Create settlement (source of truth), then accept request.
        $this->settlementService->createSettlement(
            (int) $requestLive->user_id,
            (int) $requestLive->preferred_room_id,
            SettlementService::SOURCE_REQUEST_LIVE,
        );

        $requestLive->update(['status' => 'accepted']);

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
