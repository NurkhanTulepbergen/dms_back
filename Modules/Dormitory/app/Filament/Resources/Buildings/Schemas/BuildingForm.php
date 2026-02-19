<?php

namespace Modules\Dormitory\Filament\Resources\Buildings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('total_floors')
                    ->numeric()
                    ->required()
                    ->minValue(1),
            ]);
    }
}
