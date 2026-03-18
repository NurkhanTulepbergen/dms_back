<?php

namespace Modules\Gym\Filament\Resources\GymVisits;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Gym\Filament\Resources\GymVisits\Pages\CreateGymVisit;
use Modules\Gym\Filament\Resources\GymVisits\Pages\EditGymVisit;
use Modules\Gym\Filament\Resources\GymVisits\Pages\ListGymVisits;
use Modules\Gym\Filament\Resources\GymVisits\Schemas\GymVisitForm;
use Modules\Gym\Filament\Resources\GymVisits\Tables\GymVisitsTable;
use Modules\Gym\Models\GymVisit;

class GymVisitResource extends Resource
{
    protected static ?string $model = GymVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return GymVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymVisitsTable::configure($table);
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
            'index' => ListGymVisits::route('/'),
            'create' => CreateGymVisit::route('/create'),
            'edit' => EditGymVisit::route('/{record}/edit'),
        ];
    }
}
