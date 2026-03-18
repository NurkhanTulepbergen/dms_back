<?php

namespace Modules\Gym\Filament\Resources\GymPlans;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Gym\Filament\Resources\GymPlans\Pages\CreateGymPlan;
use Modules\Gym\Filament\Resources\GymPlans\Pages\EditGymPlan;
use Modules\Gym\Filament\Resources\GymPlans\Pages\ListGymPlans;
use Modules\Gym\Filament\Resources\GymPlans\Schemas\GymPlanForm;
use Modules\Gym\Filament\Resources\GymPlans\Tables\GymPlansTable;
use Modules\Gym\Models\GymPlan;

class GymPlanResource extends Resource
{
    protected static ?string $model = GymPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GymPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymPlansTable::configure($table);
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
            'index' => ListGymPlans::route('/'),
            'create' => CreateGymPlan::route('/create'),
            'edit' => EditGymPlan::route('/{record}/edit'),
        ];
    }
}
