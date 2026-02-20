<?php

namespace Modules\Penalty\Filament\Resources\PenaltyEvidence\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\PenaltyEvidenceResource;

class EditPenaltyEvidence extends EditRecord
{
    protected static string $resource = PenaltyEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
