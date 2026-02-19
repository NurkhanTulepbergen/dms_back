<?php

namespace Modules\Dormitory\Filament\Resources\Floors;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Dormitory\Filament\Resources\Floors\Pages\CreateFloor;
use Modules\Dormitory\Filament\Resources\Floors\Pages\EditFloor;
use Modules\Dormitory\Filament\Resources\Floors\Pages\ListFloors;
use Modules\Dormitory\Filament\Resources\Floors\Schemas\FloorForm;
use Modules\Dormitory\Filament\Resources\Floors\Tables\FloorsTable;
use Modules\Dormitory\Models\Floor;

class FloorResource extends Resource
{
    protected static ?string $model = Floor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Floor';

    public static function form(Schema $schema): Schema
    {
        return FloorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FloorsTable::configure($table);
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
            'index' => ListFloors::route('/'),
            'create' => CreateFloor::route('/create'),
            'edit' => EditFloor::route('/{record}/edit'),
        ];
    }
}
