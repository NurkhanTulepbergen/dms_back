<?php

namespace Modules\Gym\Filament\Resources\GymPlans\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Gym\Filament\Resources\GymPlans\GymPlanResource;

class EditGymPlan extends EditRecord
{
    protected static string $resource = GymPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
