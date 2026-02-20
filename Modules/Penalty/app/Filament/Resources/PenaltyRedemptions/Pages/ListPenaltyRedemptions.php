<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\PenaltyRedemptionResource;

class ListPenaltyRedemptions extends ListRecords
{
    protected static string $resource = PenaltyRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
