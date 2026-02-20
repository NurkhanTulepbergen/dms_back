<?php

namespace Modules\Penalty\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Services\ChargeService;
use Modules\Penalty\Models\Penalty;
use Modules\Penalty\Models\PenaltyEvidence;
use Modules\Penalty\Models\PenaltyRule;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;
use ReflectionMethod;

class PenaltyService
{
    public function __construct(
        private readonly DisciplinePolicyService $disciplinePolicyService,
    ) {}

    public function createPenalty(array $data, int $createdBy): array
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $user = User::query()->lockForUpdate()->findOrFail((int) $data['user_id']);

            $settlement = Settlement::query()
                ->where('user_id', $user->id)
                ->whereNull('end_at')
                ->lockForUpdate()
                ->first();

            if ($settlement === null) {
                throw new BusinessException('У студента нет активного заселения', 422);
            }

            $rule = PenaltyRule::query()->findOrFail((int) $data['rule_id']);

            $points = (int) ($data['points'] ?? $rule->default_points);
            if ($points <= 0) {
                throw new BusinessException('Points must be greater than zero', 422);
            }

            $penalty = Penalty::query()->create([
                'user_id' => $user->id,
                'settlement_id' => $settlement->id,
                'rule_id' => $rule->id,
                'created_by' => $createdBy,
                'points' => $points,
                'description' => $data['description'] ?? null,
                'status' => 'active',
            ]);

            foreach ($data['evidences'] ?? [] as $path) {
                PenaltyEvidence::query()->create([
                    'penalty_id' => $penalty->id,
                    'file_path' => $path,
                ]);
            }

            $this->createFinancialChargeIfNeeded($penalty, $rule, $settlement, $user);

            $discipline = $this->disciplinePolicyService->applyIfLimitReached((int) $user->id);

            return [
                'penalty' => $penalty->load(['rule', 'evidences', 'redemptions']),
                'discipline' => $discipline,
            ];
        });
    }

    public function cancelPenalty(int $penaltyId, ?string $description = null): Penalty
    {
        return DB::transaction(function () use ($penaltyId, $description) {
            $penalty = Penalty::query()->lockForUpdate()->findOrFail($penaltyId);

            if ($penalty->status !== 'active') {
                throw new BusinessException('Только активный штраф можно отменить', 422);
            }

            $payload = ['status' => 'cancelled'];

            if ($description !== null && $description !== '') {
                $payload['description'] = $description;
            }

            $penalty->update($payload);

            return $penalty->refresh()->load(['rule', 'evidences', 'redemptions']);
        });
    }

    public function getUserPenalties(int $userId)
    {
        return Penalty::query()
            ->where('user_id', $userId)
            ->with(['rule', 'evidences', 'redemptions.reviewer'])
            ->orderByDesc('id')
            ->get();
    }

    public function getUserPenaltyById(int $userId, int $penaltyId): Penalty
    {
        return Penalty::query()
            ->where('user_id', $userId)
            ->where('id', $penaltyId)
            ->with(['rule', 'evidences', 'redemptions.reviewer'])
            ->firstOrFail();
    }

    private function createFinancialChargeIfNeeded(
        Penalty $penalty,
        PenaltyRule $rule,
        Settlement $settlement,
        User $user,
    ): void {
        if (! $rule->creates_financial_charge) {
            return;
        }

        if ($rule->financial_amount === null) {
            throw new BusinessException('financial_amount is required for financial penalty rules', 422);
        }

        $chargeService = app(ChargeService::class);

        if (! method_exists($chargeService, 'createPenaltyCharge')) {
            throw new BusinessException('ChargeService::createPenaltyCharge is not available', 500);
        }

        $reflection = new ReflectionMethod($chargeService, 'createPenaltyCharge');
        $args = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            $value = match ($name) {
                'penalty' => $penalty,
                'rule' => $rule,
                'settlement' => $settlement,
                'user' => $user,
                'amount', 'financial_amount', 'financialAmount' => (float) $rule->financial_amount,
                'user_id', 'userId' => (int) $user->id,
                'settlement_id', 'settlementId' => (int) $settlement->id,
                'penalty_id', 'penaltyId' => (int) $penalty->id,
                'type' => 'penalty',
                default => null,
            };

            if ($value === null && ! $parameter->isOptional() && $type !== null) {
                $typeName = $type->getName();

                $value = match ($typeName) {
                    Penalty::class => $penalty,
                    PenaltyRule::class => $rule,
                    Settlement::class => $settlement,
                    User::class => $user,
                    'int', 'float' => (float) $rule->financial_amount,
                    'string' => 'penalty',
                    default => null,
                };
            }

            if ($value === null && ! $parameter->isOptional()) {
                throw new BusinessException('Cannot resolve createPenaltyCharge parameter: '.$name, 500);
            }

            $args[] = $value ?? $parameter->getDefaultValue();
        }

        $reflection->invokeArgs($chargeService, $args);
    }
}
