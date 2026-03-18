<?php

namespace Modules\Gym\Filament\Resources\GymMemberships\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Gym\Filament\Resources\GymMemberships\GymMembershipResource;

class ListGymMemberships extends ListRecords
{
    protected static string $resource = GymMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
