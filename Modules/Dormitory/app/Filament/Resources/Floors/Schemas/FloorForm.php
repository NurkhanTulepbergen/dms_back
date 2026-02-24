<?php

namespace Modules\Dormitory\Filament\Resources\Floors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Dormitory\Models\Building;

class FloorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->label('Building')
                    ->relationship('building', 'address')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('floor_number')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, callable $fail) use ($get): void {
                            $buildingId = $get('building_id');

                            if (! $buildingId || $value === null) {
                                return;
                            }

                            $building = Building::query()->find($buildingId);

                            if ($building && (int) $value > (int) $building->total_floors) {
                                $fail('Номер этажа не может быть больше максимального этажа здания.');
                            }
                        };
                    }),
                Select::make('gender_policy')
                    ->options([
                        'male' => 'male',
                        'female' => 'female',
                        'mixed' => 'mixed',
                    ])
                    ->required()
                    ->default('mixed'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
