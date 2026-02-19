<?php

namespace Modules\Settlement\Filament\Resources\Settlements\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Settlement\Filament\Resources\Settlements\SettlementResource;

class CreateSettlement extends CreateRecord
{
    protected static string $resource = SettlementResource::class;
}
