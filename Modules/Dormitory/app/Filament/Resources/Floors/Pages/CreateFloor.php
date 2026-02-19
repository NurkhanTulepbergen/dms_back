<?php

namespace Modules\Dormitory\Filament\Resources\Floors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Dormitory\Filament\Resources\Floors\FloorResource;

class CreateFloor extends CreateRecord
{
    protected static string $resource = FloorResource::class;
}
