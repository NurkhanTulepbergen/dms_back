<?php

namespace Modules\Penalty\Services;

use Carbon\Carbon;
use Modules\Penalty\Models\Penalty;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;

class DisciplinePolicyService
{
    public function getActivePoints(int $userId): int
    {
        return (int) Penalty::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->sum('points');
    }

    public function applyIfLimitReached(int $userId): array
    {
        $user = User::query()->lockForUpdate()->findOrFail($userId);

        $limit = (int) ($user->discipline_limit ?? 0);
        $activePoints = $this->getActivePoints((int) $user->id);

        if ($limit <= 0 || $activePoints < $limit) {
            return [
                'active_points' => $activePoints,
                'discipline_limit' => $limit,
                'settlement_closed' => false,
            ];
        }

        $activeSettlement = Settlement::query()
            ->where('user_id', $user->id)
            ->whereNull('end_at')
            ->lockForUpdate()
            ->first();

        if ($activeSettlement === null) {
            return [
                'active_points' => $activePoints,
                'discipline_limit' => $limit,
                'settlement_closed' => false,
            ];
        }

        $activeSettlement->update([
            'end_at' => Carbon::today()->toDateString(),
            'status' => 'finished',
            'end_reason' => 'discipline',
        ]);

        return [
            'active_points' => $activePoints,
            'discipline_limit' => $limit,
            'settlement_closed' => true,
        ];
    }
}
