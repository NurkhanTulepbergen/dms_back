<?php

namespace Modules\Finance\Filament\Resources\Charges\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChargeForm
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
                Select::make('settlement_id')
                    ->label('Settlement')
                    ->relationship('settlement', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('currency')
                    ->required()
                    ->default('KZT')
                    ->maxLength(10),
                TextInput::make('type')
                    ->required()
                    ->default('semester_rent')
                    ->maxLength(255),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'paid' => 'paid',
                        'cancelled' => 'cancelled',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }
}
