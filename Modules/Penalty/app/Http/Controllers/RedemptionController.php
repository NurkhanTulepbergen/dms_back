<?php

namespace Modules\Penalty\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Penalty\Http\Requests\ApproveRedemptionRequest;
use Modules\Penalty\Http\Requests\RejectRedemptionRequest;
use Modules\Penalty\Services\RedemptionService;

class RedemptionController extends Controller
{
    public function __construct(
        private readonly RedemptionService $redemptionService,
    ) {}

    public function approve(ApproveRedemptionRequest $request, int $id)
    {
        $payload = $this->redemptionService->approve($id, (int) $request->user()->id);

        return result($payload, 200, 'Заявка на отработку одобрена');
    }

    public function reject(RejectRedemptionRequest $request, int $id)
    {
        $redemption = $this->redemptionService->reject($id, (int) $request->user()->id);

        return result($redemption, 200, 'Заявка на отработку отклонена');
    }
}
