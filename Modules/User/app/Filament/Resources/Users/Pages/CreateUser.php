<?php

namespace Modules\User\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\User\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
