<?php

namespace Modules\Requests\Filament\Resources\RequestLives\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Requests\Filament\Resources\RequestLives\RequestLiveResource;

class ListRequestLives extends ListRecords
{
    protected static string $resource = RequestLiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
