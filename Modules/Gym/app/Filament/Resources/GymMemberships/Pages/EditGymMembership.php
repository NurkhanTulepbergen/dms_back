<?php

namespace Modules\Gym\Filament\Resources\GymMemberships\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Gym\Filament\Resources\GymMemberships\GymMembershipResource;

class EditGymMembership extends EditRecord
{
    protected static string $resource = GymMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
