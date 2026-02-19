<?php

namespace Modules\User\Filament\Resources\DormStudents\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\User\Filament\Resources\DormStudents\DormStudentResource;

class EditDormStudent extends EditRecord
{
    protected static string $resource = DormStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
