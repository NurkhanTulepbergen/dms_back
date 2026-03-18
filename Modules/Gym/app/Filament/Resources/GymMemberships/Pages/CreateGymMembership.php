<?php

namespace Modules\Gym\Filament\Resources\GymMemberships\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Gym\Filament\Resources\GymMemberships\GymMembershipResource;

class CreateGymMembership extends CreateRecord
{
    protected static string $resource = GymMembershipResource::class;
}
