<?php

namespace Modules\Penalty\Filament\Resources\Penalties\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Penalty\Filament\Resources\Penalties\PenaltyResource;

class ListPenalties extends ListRecords
{
    protected static string $resource = PenaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
