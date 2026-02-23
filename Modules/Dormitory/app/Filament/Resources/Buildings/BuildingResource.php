<?php

namespace Modules\Dormitory\Filament\Resources\Buildings;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
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

    protected static function allowForCurrentAdmin(string $ability): bool
    {
        $user = Filament::auth()->user();
        $allowed = (($user?->role ?? null) === 'admin');

        Log::channel('stderr')->info('BUILDING_RESOURCE_ACCESS', [
            'ability' => $ability,
            'allowed' => $allowed,
            'user_id' => $user?->id,
            'email' => $user?->email,
            'role' => $user?->role,
            'user_class' => $user ? get_class($user) : null,
        ]);

        return $allowed;
    }

    public static function canAccess(): bool
    {
        return static::allowForCurrentAdmin('access');
    }

    public static function canViewAny(): bool
    {
        return static::allowForCurrentAdmin('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::allowForCurrentAdmin('create');
    }

    public static function canEdit($record): bool
    {
        return static::allowForCurrentAdmin('update');
    }

    public static function canDelete($record): bool
    {
        return static::allowForCurrentAdmin('delete');
    }
}
