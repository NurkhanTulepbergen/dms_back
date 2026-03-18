<?php

namespace Modules\Gym\Filament\Resources\GymPlans\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Gym\Filament\Resources\GymPlans\GymPlanResource;

class CreateGymPlan extends CreateRecord
{
    protected static string $resource = GymPlanResource::class;
}
