<?php

namespace Modules\Penalty\Filament\Resources\PenaltyEvidence\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\PenaltyEvidenceResource;

class ListPenaltyEvidence extends ListRecords
{
    protected static string $resource = PenaltyEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
