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
        $plans = $this->gymService->getAvailablePlans();

        return result($this->gymService->presentPlans($plans), 200, 'Gym plans');
    }

    public function membership(Request $request)
    {
        $user = $request->user();
        $membership = $this->gymService->getCurrentMembership($user);
        $activeVisit = $this->gymService->getActiveVisit($user);
        $availablePlans = $this->gymService->presentPlans($this->gymService->getAvailablePlans());

        if (! $membership) {
            return result([
                'has_membership' => false,
                'membership' => null,
                'available_plans' => $availablePlans,
                'has_active_visit' => (bool) $activeVisit,
                'active_visit' => $this->gymService->presentVisit($activeVisit),
            ], 200, 'Gym membership');
        }

        return result([
            'has_membership' => true,
            'membership' => $this->gymService->presentMembership($membership),
            'available_plans' => $availablePlans,
            'has_active_visit' => (bool) $activeVisit,
            'active_visit' => $this->gymService->presentVisit($activeVisit),
        ], 200, 'Gym membership');
    }

    public function createCheckout(Request $request, GymPlan $plan)
    {
        $payload = $this->gymService->purchasePlan($request->user(), $plan);

        return result($payload, 200, 'Gym checkout created');
    }

    public function checkIn(Request $request)
    {
        $payload = $this->gymService->checkIn($request->user());

        return result($payload, 200, 'Gym check-in completed');
    }

    public function completeVisit(Request $request)
    {
        $payload = $this->gymService->checkOut($request->user());

        return result($payload, 200, 'Gym check-out completed');
    }

    public function stats(Request $request)
    {
        $payload = $this->gymService->getStats($request->user());

        return result($payload, 200, 'Gym statistics');
    }
}
