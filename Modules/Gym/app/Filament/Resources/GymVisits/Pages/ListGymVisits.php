<?php

namespace Modules\Gym\Filament\Resources\GymVisits\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Gym\Filament\Resources\GymVisits\GymVisitResource;

class ListGymVisits extends ListRecords
{
    protected static string $resource = GymVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
