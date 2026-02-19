<?php

namespace Modules\Settlement\Filament\Resources\Settlements\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Settlement\Filament\Resources\Settlements\SettlementResource;

class EditSettlement extends EditRecord
{
    protected static string $resource = SettlementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
