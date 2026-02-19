<?php

namespace Modules\Settlement\Filament\Resources\Settlements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettlementForm
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
                Select::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'room_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('start_at')
                    ->required(),
                DatePicker::make('end_at'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('source'),
                TextInput::make('end_reason'),
            ]);
    }
}
