<?php

namespace Modules\Settlement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Modules\Settlement\Models\Settlement;
use Modules\Settlement\Services\SettlementService;

class SettlementController extends Controller
{
    public function __construct(
        private readonly SettlementService $settlementService,
    ) {}

    /**
     * GET /api/v1/settlements
     */
    public function index(Request $request)
    {
        $settlements = Settlement::query()
            ->with(['user', 'room.floor.building'])
            ->orderByDesc('id')
            ->get();

        return result($settlements, 200, 'Settlements');
    }

    /**
     * GET /api/v1/settlements/{id}
     */
    public function show(int $id)
    {
        $settlement = Settlement::query()
            ->with(['user', 'room.floor.building'])
            ->findOrFail($id);

        return result($settlement, 200, 'Settlement');
    }

    public function showStatus(int $userId)
    {
        $settlement = $this->settlementService->getActiveSettlement($userId, [
            'user',
            'room.floor.building',
        ]);

        return result([
            'is_living' => (bool) $settlement,
            'settlement' => $settlement,
        ], 200, 'Settlement status');
    }

    public function isLiving(int $userId)
    {
        return result([
            'user_id' => $userId,
            'is_living' => $this->settlementService->isUserLivingInDormitory($userId),
        ], 200, 'Student living status');
    }

    /**
     * POST /api/v1/settlements
     *
     * Creates a settlement (typically admin manual).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'room_id' => 'required|integer|exists:rooms,id',
            'source' => 'nullable|string|in:request_live,admin_manual,relocation',
        ]);

        $source = $validated['source'] ?? SettlementService::SOURCE_ADMIN_MANUAL;

        $settlement = $this->settlementService->createSettlement(
            (int) $validated['user_id'],
            (int) $validated['room_id'],
            $source,
        );

        return result($settlement, 201, 'Settlement created');
    }

    /**
     * PUT/PATCH /api/v1/settlements/{id}
     *
     * Supported actions:
     * - close: { end_reason }
     * - relocate: { new_room_id }
     */
    public function update(Request $request, int $id)
    {
        $settlement = Settlement::query()->findOrFail($id);

        if ($request->filled('end_reason')) {
            $validated = $request->validate([
                'end_reason' => 'required|string|in:graduation,eviction,relocation,personal',
            ]);

            $closed = $this->settlementService->closeSettlement(
                (int) $settlement->user_id,
                $validated['end_reason'],
            );

            return result($closed, 200, 'Settlement closed');
        }

        if ($request->filled('new_room_id')) {
            $validated = $request->validate([
                'new_room_id' => 'required|integer|exists:rooms,id',
            ]);

            $relocation = $this->settlementService->relocate(
                (int) $settlement->user_id,
                (int) $validated['new_room_id'],
            );

            return result($relocation, 200, 'Settlement relocated');
        }

        throw new BusinessException('Provide end_reason or new_room_id', 422);
    }

    /**
     * DELETE /api/v1/settlements/{id}
     *
     * Deleting settlements is not supported (history is stored in the same table).
     */
    public function destroy(int $id)
    {
        return result(null, 405, 'Method not allowed');
    }
}
