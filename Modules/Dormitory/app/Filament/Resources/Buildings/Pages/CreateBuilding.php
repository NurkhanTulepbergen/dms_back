<?php

namespace Modules\Dormitory\Filament\Resources\Buildings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Dormitory\Filament\Resources\Buildings\BuildingResource;

class CreateBuilding extends CreateRecord
{
    protected static string $resource = BuildingResource::class;
}
