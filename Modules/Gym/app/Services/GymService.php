<?php

namespace Modules\Gym\Services;

use App\Exceptions\BusinessException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Charge;
use Modules\Finance\Models\Payment;
use Modules\Finance\Services\StripeService;
use Modules\Gym\Models\GymMembership;
use Modules\Gym\Models\GymPlan;
use Modules\Gym\Models\GymVisit;
use Modules\User\Models\User;

class GymService
{
    private const MEMBERSHIP_STATUS_ACTIVE = 'active';
    private const MEMBERSHIP_STATUS_EXPIRED = 'expired';
    private const MEMBERSHIP_STATUS_CANCELLED = 'cancelled';
    private const MEMBERSHIP_STATUS_EXHAUSTED = 'exhausted';

    private const VISIT_STATUS_ACTIVE = 'active';
    private const VISIT_STATUS_COMPLETED = 'completed';
    private const VISIT_STATUS_CANCELLED = 'cancelled';
    private const VISIT_STATUS_AUTO_CLOSED = 'auto_closed';

    public function ensureDefaultPlansExist(): void
    {
        if (GymPlan::query()->exists()) {
            return;
        }

        GymPlan::query()->create([
            'name' => 'Месячный абонемент',
            'total_sessions' => 12,
            'price' => 10000,
            'duration_days' => 30,
            'is_active' => true,
        ]);
    }

    public function purchasePlan(User $user, GymPlan $plan): array
    {
        if (! $plan->is_active) {
            throw new BusinessException('Gym plan is not active', 422);
        }

        $this->syncMembershipStatusesForUser($user);

        $activeMembershipExists = GymMembership::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [self::MEMBERSHIP_STATUS_ACTIVE, self::MEMBERSHIP_STATUS_EXHAUSTED])
            ->whereDate('expires_at', '>=', now()->toDateString())
            ->exists();

        if ($activeMembershipExists) {
            throw new BusinessException('User already has an active gym membership', 422);
        }

        return DB::transaction(function () use ($user, $plan): array {
            $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');

            $charge = Charge::create([
                'user_id' => $user->id,
                'settlement_id' => null,
                'gym_plan_id' => $plan->id,
                'amount' => $plan->price,
                'currency' => 'KZT',
                'type' => 'gym_membership',
                'period_start' => now()->toDateString(),
                'period_end' => now()->addDays((int) $plan->duration_days)->toDateString(),
                'status' => 'pending',
            ]);

            $session = app(StripeService::class)->createCheckoutSession($charge, [
                'product_name' => $plan->name,
                'success_url' => $frontendUrl . '/gym/payment-success',
                'cancel_url' => $frontendUrl . '/payment-cancel?source=gym',
                'metadata' => [
                    'gym_plan_id' => $plan->id,
                    'source' => 'gym',
                ],
            ]);

            Payment::create([
                'charge_id' => $charge->id,
                'stripe_session_id' => $session->id,
                'amount' => $charge->amount,
            ]);

            return [
                'charge_id' => $charge->id,
                'checkout_url' => $session->url,
            ];
        });
    }

    public function activateMembership(Charge $charge): GymMembership
    {
        if ($charge->type !== 'gym_membership') {
            throw new BusinessException('Charge type is not gym_membership', 422);
        }

        $plan = GymPlan::query()->find($charge->gym_plan_id);

        if (! $plan) {
            throw new BusinessException('Gym plan not found for charge', 422);
        }

        return DB::transaction(function () use ($charge, $plan): GymMembership {
            $membership = GymMembership::query()->where('charge_id', $charge->id)->first();
            if ($membership) {
                return $membership;
            }

            return GymMembership::create([
                'user_id' => $charge->user_id,
                'plan_id' => $plan->id,
                'charge_id' => $charge->id,
                'total_sessions' => $plan->total_sessions,
                'remaining_sessions' => $plan->total_sessions,
                'started_at' => now()->toDateString(),
                'expires_at' => now()->addDays((int) $plan->duration_days)->toDateString(),
                'status' => self::MEMBERSHIP_STATUS_ACTIVE,
            ]);
        });
    }

    public function checkIn(User $user): array
    {
        return DB::transaction(function () use ($user): array {
            $membership = $this->getUsableMembership($user, true);

            $activeVisitExists = GymVisit::query()
                ->where('user_id', $user->id)
                ->where('status', self::VISIT_STATUS_ACTIVE)
                ->lockForUpdate()
                ->exists();

            if ($activeVisitExists) {
                throw new BusinessException('User already has an active gym visit', 422);
            }

            $membership->remaining_sessions = max(0, (int) $membership->remaining_sessions - 1);
            $membership->status = $this->determineMembershipStatus($membership);
            $membership->save();

            $visit = GymVisit::query()->create([
                'membership_id' => $membership->id,
                'user_id' => $user->id,
                'visit_date' => now()->toDateString(),
                'check_in_at' => now(),
                'status' => self::VISIT_STATUS_ACTIVE,
                'sessions_used' => 1,
            ]);

            return [
                'visit' => $this->formatVisit($visit),
                'membership' => $this->formatMembership($membership->refresh()),
            ];
        });
    }

    public function getCurrentMembership(User $user): ?GymMembership
    {
        $this->syncMembershipStatusesForUser($user);

        return GymMembership::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                self::MEMBERSHIP_STATUS_ACTIVE,
                self::MEMBERSHIP_STATUS_EXHAUSTED,
                self::MEMBERSHIP_STATUS_CANCELLED,
            ])
            ->latest('id')
            ->first();
    }

    public function getActiveVisit(User $user): ?GymVisit
    {
        return GymVisit::query()
            ->where('user_id', $user->id)
            ->where('status', self::VISIT_STATUS_ACTIVE)
            ->latest('id')
            ->first();
    }

    public function checkOut(User $user): array
    {
        return DB::transaction(function () use ($user): array {
            $visit = GymVisit::query()
                ->where('user_id', $user->id)
                ->where('status', self::VISIT_STATUS_ACTIVE)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (! $visit) {
                throw new BusinessException('Active gym visit not found', 422);
            }

            $checkedOutAt = now();

            $visit->update([
                'check_out_at' => $checkedOutAt,
                'duration_minutes' => max(0, $visit->check_in_at->diffInMinutes($checkedOutAt)),
                'status' => self::VISIT_STATUS_COMPLETED,
            ]);

            return $this->formatVisit($visit->refresh());
        });
    }

    public function getStats(User $user): array
    {
        /** @var Collection<int, GymVisit> $visits */
        $visits = GymVisit::query()
            ->where('user_id', $user->id)
            ->where('status', self::VISIT_STATUS_COMPLETED)
            ->orderBy('visit_date')
            ->get();

        $calendar = $visits
            ->groupBy(fn (GymVisit $visit) => $visit->visit_date->toDateString())
            ->map(fn (Collection $dateVisits, string $date) => [
                'date' => $date,
                'minutes' => (int) $dateVisits->sum('duration_minutes'),
            ])
            ->values()
            ->all();

        $weekKeys = $visits
            ->map(fn (GymVisit $visit) => $this->weekKey($visit->visit_date))
            ->unique()
            ->values();

        return [
            'total_visits' => $visits->count(),
            'total_minutes' => (int) $visits->sum('duration_minutes'),
            'current_streak_weeks' => $this->calculateCurrentWeekStreak($weekKeys),
            'best_streak_weeks' => $this->calculateBestWeekStreak($weekKeys),
            'calendar' => $calendar,
        ];
    }

    public function autoCloseVisits(): int
    {
        $closedVisits = 0;

        GymVisit::query()
            ->where('status', self::VISIT_STATUS_ACTIVE)
            ->where('check_in_at', '<=', now()->subHours(4))
            ->orderBy('id')
            ->chunkById(100, function (Collection $visits) use (&$closedVisits): void {
                foreach ($visits as $visit) {
                    DB::transaction(function () use ($visit, &$closedVisits): void {
                        $lockedVisit = GymVisit::query()
                            ->whereKey($visit->id)
                            ->where('status', self::VISIT_STATUS_ACTIVE)
                            ->lockForUpdate()
                            ->first();

                        if (! $lockedVisit) {
                            return;
                        }

                        $checkedOutAt = now();

                        $lockedVisit->update([
                            'check_out_at' => $checkedOutAt,
                            'duration_minutes' => max(0, $lockedVisit->check_in_at->diffInMinutes($checkedOutAt)),
                            'status' => self::VISIT_STATUS_AUTO_CLOSED,
                        ]);

                        $closedVisits++;
                    });
                }
            });

        return $closedVisits;
    }

    private function getUsableMembership(User $user, bool $lockForUpdate = false): GymMembership
    {
        $query = GymMembership::query()
            ->where('user_id', $user->id)
            ->latest('id');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        /** @var GymMembership|null $membership */
        $membership = $query->first();

        if (! $membership) {
            throw new BusinessException('Active gym membership not found', 422);
        }

        $membership->status = $this->determineMembershipStatus($membership);
        $membership->save();

        if ($membership->status === self::MEMBERSHIP_STATUS_EXPIRED) {
            throw new BusinessException('Gym membership is expired', 422);
        }

        if ($membership->status === self::MEMBERSHIP_STATUS_CANCELLED) {
            throw new BusinessException('Gym membership is cancelled', 422);
        }

        if ((int) $membership->remaining_sessions <= 0) {
            throw new BusinessException('No remaining gym sessions', 422);
        }

        return $membership;
    }

    private function syncMembershipStatusesForUser(User $user): void
    {
        GymMembership::query()
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get()
            ->each(function (GymMembership $membership): void {
                $status = $this->determineMembershipStatus($membership);

                if ($membership->status !== $status) {
                    $membership->update(['status' => $status]);
                }
            });
    }

    private function determineMembershipStatus(GymMembership $membership): string
    {
        if ($membership->status === self::MEMBERSHIP_STATUS_CANCELLED) {
            return self::MEMBERSHIP_STATUS_CANCELLED;
        }

        if ($membership->expires_at->isPast() && ! $membership->expires_at->isToday()) {
            return self::MEMBERSHIP_STATUS_EXPIRED;
        }

        if ((int) $membership->remaining_sessions <= 0) {
            return self::MEMBERSHIP_STATUS_EXHAUSTED;
        }

        return self::MEMBERSHIP_STATUS_ACTIVE;
    }

    private function formatMembership(GymMembership $membership): array
    {
        return [
            'id' => $membership->id,
            'remaining_sessions' => (int) $membership->remaining_sessions,
            'expires_at' => $membership->expires_at?->toDateString(),
            'status' => $membership->status,
        ];
    }

    private function formatVisit(GymVisit $visit): array
    {
        return [
            'id' => $visit->id,
            'membership_id' => $visit->membership_id,
            'visit_date' => $visit->visit_date?->toDateString(),
            'check_in_at' => $visit->check_in_at?->toISOString(),
            'check_out_at' => $visit->check_out_at?->toISOString(),
            'duration_minutes' => $visit->duration_minutes !== null ? (int) $visit->duration_minutes : null,
            'sessions_used' => (int) $visit->sessions_used,
            'status' => $visit->status,
        ];
    }

    public function presentVisit(?GymVisit $visit): ?array
    {
        if (! $visit) {
            return null;
        }

        return $this->formatVisit($visit);
    }

    private function calculateCurrentWeekStreak(Collection $weekKeys): int
    {
        if ($weekKeys->isEmpty()) {
            return 0;
        }

        $weekSet = $weekKeys->flip();
        $cursor = now()->startOfWeek(CarbonInterface::MONDAY);
        $streak = 0;

        while ($weekSet->has($this->weekKey($cursor))) {
            $streak++;
            $cursor->subWeek();
        }

        return $streak;
    }

    private function calculateBestWeekStreak(Collection $weekKeys): int
    {
        if ($weekKeys->isEmpty()) {
            return 0;
        }

        $best = 0;
        $current = 0;
        $previousWeek = null;

        foreach ($weekKeys as $weekKey) {
            [$year, $week] = array_map('intval', explode('-', (string) $weekKey));

            $currentWeek = now()
                ->setISODate($year, $week)
                ->startOfWeek(CarbonInterface::MONDAY);

            if ($previousWeek && $previousWeek->copy()->addWeek()->equalTo($currentWeek)) {
                $current++;
            } else {
                $current = 1;
            }

            $best = max($best, $current);
            $previousWeek = $currentWeek;
        }

        return $best;
    }

    private function weekKey(CarbonInterface $date): string
    {
        return $date->copy()->startOfWeek(CarbonInterface::MONDAY)->format('o-W');
    }
}
