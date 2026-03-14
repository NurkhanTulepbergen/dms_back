<?php

namespace Modules\Gym\Services;

use App\Exceptions\BusinessException;
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
    public function purchasePlan(User $user, GymPlan $plan): array
    {
        if (! $plan->is_active) {
            throw new BusinessException('Gym plan is not active', 422);
        }

        $activeMembershipExists = GymMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereDate('expires_at', '>', now()->toDateString())
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
                'success_url' => $frontendUrl . '/payment-success?source=gym',
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
                'status' => 'active',
            ]);
        });
    }

    public function useSession(User $user): GymMembership
    {
        return DB::transaction(function () use ($user): GymMembership {
            /** @var GymMembership|null $activeMembership */
            $activeMembership = GymMembership::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (! $activeMembership) {
                throw new BusinessException('Active gym membership not found', 422);
            }

            if ($activeMembership->expires_at->toDateString() <= now()->toDateString()) {
                $activeMembership->status = 'expired';
                $activeMembership->save();

                throw new BusinessException('Gym membership is expired', 422);
            }

            if ((int) $activeMembership->remaining_sessions <= 0) {
                throw new BusinessException('No remaining gym sessions', 422);
            }

            GymVisit::create([
                'membership_id' => $activeMembership->id,
                'used_at' => now(),
            ]);

            $remaining = (int) $activeMembership->remaining_sessions - 1;
            $activeMembership->remaining_sessions = $remaining;
            if ($remaining === 0) {
                $activeMembership->status = 'expired';
            }
            $activeMembership->save();

            return $activeMembership->refresh();
        });
    }

    public function getCurrentMembership(User $user): ?GymMembership
    {
        return GymMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereDate('expires_at', '>', now()->toDateString())
            ->latest('id')
            ->first();
    }
}
