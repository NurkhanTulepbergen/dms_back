<?php

namespace Modules\User\Filament\Resources\DormStudents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\User\Filament\Resources\DormStudents\DormStudentResource;

class ListDormStudents extends ListRecords
{
    protected static string $resource = DormStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
