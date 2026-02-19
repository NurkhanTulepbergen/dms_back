<?php

namespace Modules\Dormitory\Filament\Resources\Floors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Dormitory\Filament\Resources\Floors\FloorResource;

class ListFloors extends ListRecords
{
    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
