<?php

namespace Modules\Dormitory\Filament\Resources\Floors\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Dormitory\Filament\Resources\Floors\FloorResource;

class EditFloor extends EditRecord
{
    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
