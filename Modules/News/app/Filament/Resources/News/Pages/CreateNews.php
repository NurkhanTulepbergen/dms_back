<?php

namespace Modules\News\Filament\Resources\News\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\News\Filament\Resources\News\NewsResource;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
}
