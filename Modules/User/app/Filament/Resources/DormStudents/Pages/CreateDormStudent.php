<?php

namespace Modules\User\Filament\Resources\DormStudents\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\User\Filament\Resources\DormStudents\DormStudentResource;

class CreateDormStudent extends CreateRecord
{
    protected static string $resource = DormStudentResource::class;
}
