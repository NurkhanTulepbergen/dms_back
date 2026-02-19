<?php

namespace Modules\Requests\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Modules\Requests\Models\RequestChangeRoom;
use Modules\Settlement\Services\SettlementService;
use Modules\User\Models\DormStudent;

class RequestChangeRoomService
{
    public function __construct(
        private readonly SettlementService $settlementService,
    ) {}

    public function createRequest(int $userId, int $roomId, ?string $description): RequestChangeRoom
    {
        $dormStudent = DormStudent::query()->firstOrCreate(
            ['user_id' => $userId],
            ['warning_count' => 0]
        );

        $hasPending = RequestChangeRoom::query()
            ->where('student_id', $dormStudent->user_id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            throw new BusinessException('У вас уже есть активная заявка на смену комнаты', 422);
        }

        return RequestChangeRoom::create([
            'student_id' => $dormStudent->user_id,
            'room_id' => $roomId,
            'description' => $description,
            'status' => 'pending',
        ]);
    }

    public function approve(int $requestId): array
    {
        return DB::transaction(function () use ($requestId) {
            $requestChange = RequestChangeRoom::query()->lockForUpdate()->findOrFail($requestId);

            if ($requestChange->status !== 'pending') {
                throw new BusinessException('Заявка уже обработана', 422);
            }

            $relocation = $this->settlementService->relocate(
                (int) $requestChange->student_id,
                (int) $requestChange->room_id,
            );

            $requestChange->update(['status' => 'accepted']);

            return [
                'request' => $requestChange->refresh(),
                'relocation' => $relocation,
            ];
        });
    }

    public function reject(int $requestId): RequestChangeRoom
    {
        return DB::transaction(function () use ($requestId) {
            $requestChange = RequestChangeRoom::query()->lockForUpdate()->findOrFail($requestId);

            if ($requestChange->status !== 'pending') {
                throw new BusinessException('Заявка уже обработана', 422);
            }

            $requestChange->update(['status' => 'rejected']);

            return $requestChange->refresh();
        });
    }
}

