<?php

namespace Modules\Gym\Filament\Resources\GymPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GymPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('total_sessions')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('duration_days')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
