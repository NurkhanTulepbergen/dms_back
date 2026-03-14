<?php

namespace Modules\Gym\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Gym\Models\GymPlan;
use Modules\Gym\Services\GymService;

class GymController extends Controller
{
    public function __construct(
        private readonly GymService $gymService,
    ) {}

    public function plans()
    {
        $this->gymService->ensureDefaultPlansExist();

        $plans = GymPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return result($plans, 200, 'Gym plans');
    }

    public function membership(Request $request)
    {
        $membership = $this->gymService->getCurrentMembership($request->user());

        if (! $membership) {
            return result([
                'has_membership' => false,
            ], 200, 'Gym membership');
        }

        return result([
            'has_membership' => true,
            'remaining_sessions' => (int) $membership->remaining_sessions,
            'expires_at' => $membership->expires_at?->toDateString(),
            'status' => $membership->status,
        ], 200, 'Gym membership');
    }

    public function checkout(Request $request, GymPlan $plan)
    {
        $payload = $this->gymService->purchasePlan($request->user(), $plan);

        return result($payload, 200, 'Gym checkout created');
    }

    public function useSession(Request $request)
    {
        $membership = $this->gymService->useSession($request->user());

        return result([
            'remaining_sessions' => (int) $membership->remaining_sessions,
            'expires_at' => $membership->expires_at?->toDateString(),
            'status' => $membership->status,
        ], 200, 'Gym session used');
    }
}
