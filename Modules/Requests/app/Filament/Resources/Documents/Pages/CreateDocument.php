<?php

namespace Modules\Requests\Filament\Resources\Documents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Requests\Filament\Resources\Documents\DocumentResource;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
}
