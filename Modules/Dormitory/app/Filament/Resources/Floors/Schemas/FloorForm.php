<?php

namespace Modules\Dormitory\Filament\Resources\Floors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

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
                    ->minValue(1),
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
