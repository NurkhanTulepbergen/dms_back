<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRules\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Penalty\Filament\Resources\PenaltyRules\PenaltyRuleResource;

class CreatePenaltyRule extends CreateRecord
{
    protected static string $resource = PenaltyRuleResource::class;
}
