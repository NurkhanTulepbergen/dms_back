<?php

namespace Modules\Dormitory\Filament\Resources\Buildings;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Dormitory\Filament\Resources\Buildings\Pages\CreateBuilding;
use Modules\Dormitory\Filament\Resources\Buildings\Pages\EditBuilding;
use Modules\Dormitory\Filament\Resources\Buildings\Pages\ListBuildings;
use Modules\Dormitory\Filament\Resources\Buildings\Schemas\BuildingForm;
use Modules\Dormitory\Filament\Resources\Buildings\Tables\BuildingsTable;
use Modules\Dormitory\Models\Building;

class BuildingResource extends Resource
{
    protected static ?string $model = Building::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Building';

    public static function form(Schema $schema): Schema
    {
        return BuildingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuildingsTable::configure($table);
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
            'index' => ListBuildings::route('/'),
            'create' => CreateBuilding::route('/create'),
            'edit' => EditBuilding::route('/{record}/edit'),
        ];
    }
}
