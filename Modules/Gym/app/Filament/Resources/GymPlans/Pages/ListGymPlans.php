<?php

namespace Modules\Gym\Filament\Resources\GymPlans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Gym\Filament\Resources\GymPlans\GymPlanResource;

class ListGymPlans extends ListRecords
{
    protected static string $resource = GymPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
