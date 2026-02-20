<?php

namespace Modules\Penalty\Filament\Resources\Penalties\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Penalty\Filament\Resources\Penalties\PenaltyResource;

class EditPenalty extends EditRecord
{
    protected static string $resource = PenaltyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
