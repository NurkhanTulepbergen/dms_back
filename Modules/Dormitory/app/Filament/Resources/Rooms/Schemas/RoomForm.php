<?php

namespace Modules\Dormitory\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Models\RoomType;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->label('Building')
                    ->options(fn () => Building::query()->orderBy('address')->pluck('address', 'id')->toArray())
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, ?Room $record): void {
                        $component->state($record?->floor?->building_id);
                    })
                    ->afterStateUpdated(function (callable $set): void {
                        $set('floor_id', null);
                    }),
                Select::make('floor_id')
                    ->label('Floor')
                    ->options(function (callable $get): array {
                        $buildingId = $get('building_id');

                        if (! $buildingId) {
                            return [];
                        }

                        return Floor::query()
                            ->where('building_id', $buildingId)
                            ->orderBy('floor_number')
                            ->pluck('floor_number', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('room_type_id')
                    ->label('Room Type')
                    ->options(fn () => RoomType::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('room_number')
                    ->numeric()
                    ->required(),
                TextInput::make('capacity')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                TextInput::make('live_cap')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
