<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRules\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Penalty\Filament\Resources\PenaltyRules\PenaltyRuleResource;

class EditPenaltyRule extends EditRecord
{
    protected static string $resource = PenaltyRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
