<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRules\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Penalty\Filament\Resources\PenaltyRules\PenaltyRuleResource;

class ListPenaltyRules extends ListRecords
{
    protected static string $resource = PenaltyRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
