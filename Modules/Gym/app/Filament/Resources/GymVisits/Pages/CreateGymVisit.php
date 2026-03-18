<?php

namespace Modules\Gym\Filament\Resources\GymVisits\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Gym\Filament\Resources\GymVisits\GymVisitResource;

class CreateGymVisit extends CreateRecord
{
    protected static string $resource = GymVisitResource::class;
}
