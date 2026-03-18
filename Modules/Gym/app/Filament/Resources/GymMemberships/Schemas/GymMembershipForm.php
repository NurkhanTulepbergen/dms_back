<?php

namespace Modules\Gym\Filament\Resources\GymMemberships\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GymMembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('charge_id')
                    ->label('Charge')
                    ->relationship('charge', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('total_sessions')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('remaining_sessions')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                DatePicker::make('started_at')
                    ->required(),
                DatePicker::make('expires_at')
                    ->required(),
                Select::make('status')
                    ->options([
                        'active' => 'active',
                        'expired' => 'expired',
                        'cancelled' => 'cancelled',
                        'exhausted' => 'exhausted',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }
}
