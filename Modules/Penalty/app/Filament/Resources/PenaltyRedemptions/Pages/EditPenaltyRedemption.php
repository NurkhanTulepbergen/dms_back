<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\PenaltyRedemptionResource;

class EditPenaltyRedemption extends EditRecord
{
    protected static string $resource = PenaltyRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
