<?php

namespace Modules\Penalty\Services;

use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
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
            $rule = PenaltyRule::query()->findOrFail((int) $data['rule_id']);
            $points = (int) ($data['points'] ?? $rule->default_points);

            if ($points <= 0) {
                throw new BusinessException('Points must be greater than zero', 422);
            }

            if (! empty($data['room_id'])) {
                $roomId = (int) $data['room_id'];
                $settlements = $this->getActiveRoomSettlements($roomId);

                if ($settlements->isEmpty()) {
                    throw new BusinessException('В выбранной комнате нет активных студентов', 422);
                }

                $createdPenalties = [];
                $disciplines = [];

                foreach ($settlements as $settlement) {
                    $payload = $this->createPenaltyForSettlement(
                        $settlement->user,
                        $settlement,
                        $rule,
                        $points,
                        $data['description'] ?? null,
                        $createdBy,
                        $data['evidences'] ?? [],
                    );

                    $createdPenalties[] = $payload['penalty'];

                    if ($payload['discipline'] !== null) {
                        $disciplines[] = $payload['discipline'];
                    }
                }

                return [
                    'penalties' => $createdPenalties,
                    'disciplines' => $disciplines,
                    'target_type' => 'room',
                    'room_id' => $roomId,
                    'affected_users_count' => count($createdPenalties),
                ];
            }

            $user = User::query()->lockForUpdate()->findOrFail((int) $data['user_id']);
            $settlement = $this->getActiveUserSettlement($user->id);

            return $this->createPenaltyForSettlement(
                $user,
                $settlement,
                $rule,
                $points,
                $data['description'] ?? null,
                $createdBy,
                $data['evidences'] ?? [],
            );
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

    public function getManagePenalties(array $filters = [])
    {
        $query = Penalty::query()
            ->with([
                'user',
                'creator',
                'settlement.room',
                'rule',
                'evidences',
                'redemptions.user',
                'redemptions.reviewer',
            ])
            ->orderByDesc('id');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('description', 'like', "%{$search}%")
                    ->orWhereHas('rule', function (Builder $ruleQuery) use ($search) {
                        $ruleQuery
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('middlename', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('uni_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('settlement.room', function (Builder $roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        $status = $filters['status'] ?? null;
        if (is_string($status) && $status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        $redemptionStatus = $filters['redemption_status'] ?? null;
        if (is_string($redemptionStatus) && $redemptionStatus !== '' && $redemptionStatus !== 'all') {
            $query->whereHas('redemptions', function (Builder $redemptionQuery) use ($redemptionStatus) {
                $redemptionQuery->where('status', $redemptionStatus);
            });
        }

        return $query->get();
    }

    public function getPenaltyRules()
    {
        return PenaltyRule::query()
            ->orderBy('title')
            ->get();
    }

    public function getPenaltyTargets(?string $search = null, int $limit = 50)
    {
        $query = Settlement::query()
            ->with(['user', 'room'])
            ->whereNull('end_at')
            ->whereHas('user', function (Builder $builder) {
                $builder->where('role', 'student');
            })
            ->orderByDesc('id')
            ->limit($limit);

        $search = trim((string) $search);
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->whereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('middlename', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('uni_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function (Builder $roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get();
    }

    public function getPenaltyRoomTargets(?string $search = null, int $limit = 50)
    {
        $query = Settlement::query()
            ->with(['user', 'room'])
            ->whereNull('end_at')
            ->whereHas('user', function (Builder $builder) {
                $builder->where('role', 'student');
            })
            ->orderBy('room_id')
            ->orderByDesc('id');

        $search = trim((string) $search);

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->whereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('middlename', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('uni_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function (Builder $roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get()
            ->groupBy('room_id')
            ->take($limit);
    }

    private function createPenaltyForSettlement(
        User $user,
        Settlement $settlement,
        PenaltyRule $rule,
        int $points,
        ?string $description,
        int $createdBy,
        array $evidencePaths = [],
    ): array {
        $penalty = Penalty::query()->create([
            'user_id' => $user->id,
            'settlement_id' => $settlement->id,
            'rule_id' => $rule->id,
            'created_by' => $createdBy,
            'points' => $points,
            'description' => $description,
            'status' => 'active',
        ]);

        foreach ($evidencePaths as $path) {
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
    }

    private function getActiveUserSettlement(int $userId): Settlement
    {
        $settlement = Settlement::query()
            ->with('user')
            ->where('user_id', $userId)
            ->whereNull('end_at')
            ->lockForUpdate()
            ->first();

        if ($settlement === null) {
            throw new BusinessException('У студента нет активного заселения', 422);
        }

        return $settlement;
    }

    private function getActiveRoomSettlements(int $roomId): Collection
    {
        return Settlement::query()
            ->with(['user', 'room'])
            ->where('room_id', $roomId)
            ->whereNull('end_at')
            ->whereHas('user', function (Builder $builder) {
                $builder->where('role', 'student');
            })
            ->lockForUpdate()
            ->get();
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
