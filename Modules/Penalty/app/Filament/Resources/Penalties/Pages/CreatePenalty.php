<?php

namespace Modules\Penalty\Filament\Resources\Penalties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Penalty\Filament\Resources\Penalties\PenaltyResource;

class CreatePenalty extends CreateRecord
{
    protected static string $resource = PenaltyResource::class;
}
