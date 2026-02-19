<?php

namespace Modules\Requests\Filament\Resources\RequestChangeRooms\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requests\Filament\Resources\RequestChangeRooms\RequestChangeRoomResource;

class ListRequestChangeRooms extends ListRecords
{
    protected static string $resource = RequestChangeRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
