<?php

namespace Modules\Penalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Penalty\Http\Requests\CancelPenaltyRequest;
use Modules\Penalty\Http\Requests\StorePenaltyRedemptionRequest;
use Modules\Penalty\Http\Requests\StorePenaltyRequest;
use Modules\Penalty\Services\PenaltyService;
use Modules\Penalty\Services\RedemptionService;

class PenaltyController extends Controller
{
    public function __construct(
        private readonly PenaltyService $penaltyService,
        private readonly RedemptionService $redemptionService,
    ) {}

    public function index(Request $request)
    {
        $penalties = $this->penaltyService->getUserPenalties((int) $request->user()->id);

        return result($penalties, 200, 'Штрафы пользователя');
    }

    public function show(Request $request, int $id)
    {
        $penalty = $this->penaltyService->getUserPenaltyById((int) $request->user()->id, $id);

        return result($penalty, 200, 'Штраф');
    }

    public function store(StorePenaltyRequest $request)
    {
        $payload = $this->penaltyService->createPenalty(
            $request->validated(),
            (int) $request->user()->id,
        );

        return result($payload, 201, 'Штраф создан');
    }

    public function cancel(CancelPenaltyRequest $request, int $id)
    {
        $penalty = $this->penaltyService->cancelPenalty(
            $id,
            $request->validated()['description'] ?? null,
        );

        return result($penalty, 200, 'Штраф отменен');
    }

    public function redeem(StorePenaltyRedemptionRequest $request, int $id)
    {
        $redemption = $this->redemptionService->createRedemption(
            $id,
            (int) $request->user()->id,
            $request->validated(),
        );

        return result($redemption, 201, 'Заявка на отработку отправлена');
    }
}
