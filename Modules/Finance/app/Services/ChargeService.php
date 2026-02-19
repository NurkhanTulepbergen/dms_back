<?php

namespace Modules\Finance\Services;

use Illuminate\Database\QueryException;
use Modules\Finance\Models\Charge;
use Modules\Settlement\Models\Settlement;
use App\Exceptions\BusinessException;

class ChargeService
{
    public function createSemesterCharge(Settlement $settlement): Charge
    {
        $room = $settlement->room()->with('roomType')->first();

        if (!$room || !$room->roomType) {
            throw new BusinessException('Room type is not defined for settlement', 422);
        }

        try {
            return Charge::firstOrCreate(
                [
                    'settlement_id' => $settlement->id,
                    'type' => 'semester_rent',
                ],
                [
                    'user_id' => $settlement->user_id,
                    'amount' => $room->roomType->semester_price,
                    'currency' => 'KZT',
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->addMonths(5)->endOfMonth(),
                    'status' => 'pending',
                ]
            );
        } catch (QueryException) {
            // Fallback for concurrent insert race when unique index is hit.
            return Charge::query()
                ->where('settlement_id', $settlement->id)
                ->where('type', 'semester_rent')
                ->firstOrFail();
        }
    }
}
