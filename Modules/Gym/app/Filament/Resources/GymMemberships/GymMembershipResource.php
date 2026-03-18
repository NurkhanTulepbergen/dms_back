<?php

namespace Modules\Gym\Filament\Resources\GymMemberships;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Gym\Filament\Resources\GymMemberships\Pages\CreateGymMembership;
use Modules\Gym\Filament\Resources\GymMemberships\Pages\EditGymMembership;
use Modules\Gym\Filament\Resources\GymMemberships\Pages\ListGymMemberships;
use Modules\Gym\Filament\Resources\GymMemberships\Schemas\GymMembershipForm;
use Modules\Gym\Filament\Resources\GymMemberships\Tables\GymMembershipsTable;
use Modules\Gym\Models\GymMembership;

class GymMembershipResource extends Resource
{
    protected static ?string $model = GymMembership::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return GymMembershipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GymMembershipsTable::configure($table);
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
            'index' => ListGymMemberships::route('/'),
            'create' => CreateGymMembership::route('/create'),
            'edit' => EditGymMembership::route('/{record}/edit'),
        ];
    }
}
