<?php

namespace Modules\Gym\Filament\Resources\GymVisits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GymVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('membership_id')
                    ->label('Membership')
                    ->relationship('membership', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('visit_date')
                    ->required(),
                DateTimePicker::make('check_in_at')
                    ->required(),
                DateTimePicker::make('check_out_at'),
                TextInput::make('duration_minutes')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('sessions_used')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),
                Select::make('status')
                    ->options([
                        'active' => 'active',
                        'completed' => 'completed',
                        'cancelled' => 'cancelled',
                        'auto_closed' => 'auto_closed',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }
}
