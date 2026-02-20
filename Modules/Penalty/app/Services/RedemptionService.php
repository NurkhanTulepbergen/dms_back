<?php

namespace Modules\Penalty\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Modules\Penalty\Models\Penalty;
use Modules\Penalty\Models\PenaltyRedemption;

class RedemptionService
{
    public function createRedemption(int $penaltyId, int $userId, array $data): PenaltyRedemption
    {
        return DB::transaction(function () use ($penaltyId, $userId, $data) {
            $penalty = Penalty::query()
                ->with('rule')
                ->lockForUpdate()
                ->findOrFail($penaltyId);

            if ((int) $penalty->user_id !== $userId) {
                throw new BusinessException('Нет доступа к данному штрафу', 403);
            }

            if ($penalty->status !== 'active') {
                throw new BusinessException('Отработка доступна только для активного штрафа', 422);
            }

            if (! $penalty->rule || ! $penalty->rule->redeemable) {
                throw new BusinessException('Этот штраф не подлежит отработке', 422);
            }

            $existsPending = PenaltyRedemption::query()
                ->where('penalty_id', $penalty->id)
                ->where('status', 'pending')
                ->exists();

            if ($existsPending) {
                throw new BusinessException('У штрафа уже есть заявка на отработку в статусе pending', 422);
            }

            return PenaltyRedemption::query()->create([
                'penalty_id' => $penalty->id,
                'user_id' => $userId,
                'event_type' => $data['event_type'],
                'description' => $data['description'],
                'file_path' => $data['file_path'] ?? null,
                'status' => 'pending',
            ])->load(['penalty', 'user']);
        });
    }

    public function approve(int $redemptionId, int $reviewerId): array
    {
        return DB::transaction(function () use ($redemptionId, $reviewerId) {
            $redemption = PenaltyRedemption::query()
                ->with('penalty')
                ->lockForUpdate()
                ->findOrFail($redemptionId);

            if ($redemption->status !== 'pending') {
                throw new BusinessException('Заявка уже обработана', 422);
            }

            if ($redemption->penalty->status !== 'active') {
                throw new BusinessException('Связанный штраф уже не активен', 422);
            }

            $redemption->update([
                'status' => 'approved',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            $redemption->penalty->update([
                'status' => 'resolved',
            ]);

            return [
                'redemption' => $redemption->refresh()->load(['penalty', 'user', 'reviewer']),
                'penalty' => $redemption->penalty->refresh(),
            ];
        });
    }

    public function reject(int $redemptionId, int $reviewerId): PenaltyRedemption
    {
        return DB::transaction(function () use ($redemptionId, $reviewerId) {
            $redemption = PenaltyRedemption::query()
                ->lockForUpdate()
                ->findOrFail($redemptionId);

            if ($redemption->status !== 'pending') {
                throw new BusinessException('Заявка уже обработана', 422);
            }

            $redemption->update([
                'status' => 'rejected',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            return $redemption->refresh()->load(['penalty', 'user', 'reviewer']);
        });
    }
}
