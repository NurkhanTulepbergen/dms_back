<?php

namespace Modules\Gym\Filament\Resources\GymVisits\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Gym\Filament\Resources\GymVisits\GymVisitResource;

class EditGymVisit extends EditRecord
{
    protected static string $resource = GymVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
