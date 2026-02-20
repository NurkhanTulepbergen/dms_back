<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\PenaltyRedemptionResource;

class CreatePenaltyRedemption extends CreateRecord
{
    protected static string $resource = PenaltyRedemptionResource::class;
}
