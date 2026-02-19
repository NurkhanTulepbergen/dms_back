<?php

namespace Modules\User\Filament\Resources\DormStudents;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\User\Filament\Resources\DormStudents\Pages\CreateDormStudent;
use Modules\User\Filament\Resources\DormStudents\Pages\EditDormStudent;
use Modules\User\Filament\Resources\DormStudents\Pages\ListDormStudents;
use Modules\User\Filament\Resources\DormStudents\Schemas\DormStudentForm;
use Modules\User\Filament\Resources\DormStudents\Tables\DormStudentsTable;
use Modules\User\Models\DormStudent;

class DormStudentResource extends Resource
{
    protected static ?string $model = DormStudent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DormStudent';

    public static function form(Schema $schema): Schema
    {
        return DormStudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DormStudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDormStudents::route('/'),
            'create' => CreateDormStudent::route('/create'),
            'edit' => EditDormStudent::route('/{record}/edit'),
        ];
    }
}
