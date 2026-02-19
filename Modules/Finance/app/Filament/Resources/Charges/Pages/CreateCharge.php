<?php

namespace Modules\Finance\Filament\Resources\Charges\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Finance\Filament\Resources\Charges\ChargeResource;

class CreateCharge extends CreateRecord
{
    protected static string $resource = ChargeResource::class;
}
