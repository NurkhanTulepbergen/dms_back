<?php

namespace Modules\Settlement\Services;

use App\Exceptions\BusinessException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Services\ChargeService;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;

class SettlementService
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELLED = 'cancelled';

    public const SOURCE_REQUEST_LIVE = 'request_live';
    public const SOURCE_ADMIN_MANUAL = 'admin_manual';
    public const SOURCE_RELOCATION = 'relocation';

    public const END_REASON_GRADUATION = 'graduation';
    public const END_REASON_EVICTION = 'eviction';
    public const END_REASON_RELOCATION = 'relocation';
    public const END_REASON_PERSONAL = 'personal';

    public function getActiveSettlement(int $userId, array $with = []): ?Settlement
    {
        return Settlement::query()
            ->with($with)
            ->where('user_id', $userId)
            ->where('status', self::STATUS_ACTIVE)
            ->whereNull('end_at')
            ->latest('start_at')
            ->first();
    }

    public function isUserLivingInDormitory(int $userId): bool
    {
        return $this->getActiveSettlement($userId) !== null;
    }

    public function createSettlement(int $userId, int $roomId, string $source): Settlement
    {
        if (!in_array($source, [
            self::SOURCE_REQUEST_LIVE,
            self::SOURCE_ADMIN_MANUAL,
            self::SOURCE_RELOCATION,
        ], true)) {
            throw new BusinessException('Invalid settlement source', 422);
        }

        return DB::transaction(function () use ($userId, $roomId, $source) {
            /** @var User $user */
            $user = User::query()->lockForUpdate()->findOrFail($userId);

            $gender = $user->gender;
            if ($gender === null || !in_array($gender, ['male', 'female'], true)) {
                throw new BusinessException('User gender is required (male/female)', 422);
            }

            // Ensure: only one active settlement.
            $existingActive = Settlement::query()
                ->where('user_id', $user->id)
                ->whereNull('end_at')
                ->lockForUpdate()
                ->first();

            if ($existingActive !== null) {
                throw new BusinessException('User already has an active settlement', 422);
            }

            /** @var Room $room */
            $room = Room::query()
                ->with(['floor', 'roomType'])
                ->lockForUpdate()
                ->findOrFail($roomId);

            if (isset($room->is_active) && ! (bool) $room->is_active) {
                throw new BusinessException('Room is not active', 422);
            }

            $policy = $room->floor?->gender_policy ?? 'mixed';
            if (!in_array($policy, ['male', 'female', 'mixed'], true)) {
                throw new BusinessException('Invalid floor gender policy', 422);
            }
            if (isset($room->floor?->is_active) && ! (bool) $room->floor->is_active) {
                throw new BusinessException('Floor is not active', 422);
            }
            if ($policy !== 'mixed' && $policy !== $gender) {
                throw new BusinessException('Gender policy does not allow placement on this floor', 422);
            }

            $activeOccupancy = Settlement::query()
                ->where('room_id', $room->id)
                ->whereNull('end_at')
                ->lockForUpdate()
                ->count();

            if ($activeOccupancy >= (int) $room->capacity) {
                throw new BusinessException('Room capacity exceeded', 422);
            }

            $today = Carbon::today()->toDateString();


            $settlement = Settlement::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'start_at' => $today,
                'end_at' => null,
                'status' => self::STATUS_ACTIVE,
                'source' => $source,
                'end_reason' => null,
            ]);

            app(ChargeService::class)
                ->createSemesterCharge($settlement);

            return $settlement;
        });
    }

    public function closeSettlement(int $userId, string $endReason): Settlement
    {
        if (!in_array($endReason, [
            self::END_REASON_GRADUATION,
            self::END_REASON_EVICTION,
            self::END_REASON_RELOCATION,
            self::END_REASON_PERSONAL,
        ], true)) {
            throw new BusinessException('Invalid end reason', 422);
        }

        return DB::transaction(function () use ($userId, $endReason) {
            $active = Settlement::query()
                ->where('user_id', $userId)
                ->whereNull('end_at')
                ->lockForUpdate()
                ->first();

            if ($active === null) {
                throw new BusinessException('Active settlement not found', 404);
            }

            $today = Carbon::today()->toDateString();

            $active->update([
                'end_at' => $today,
                'status' => self::STATUS_FINISHED,
                'end_reason' => $endReason,
            ]);

            return $active->refresh();
        });
    }

    public function relocate(int $userId, int $newRoomId): array
    {
        return DB::transaction(function () use ($userId, $newRoomId) {
            $closed = $this->closeSettlement($userId, self::END_REASON_RELOCATION);
            $created = $this->createSettlement($userId, $newRoomId, self::SOURCE_RELOCATION);

            return [
                'closed' => $closed,
                'created' => $created,
            ];
        });
    }
}
