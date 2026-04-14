<?php

namespace Modules\Penalty\Filament\Resources\Penalties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenaltyForm
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
                Select::make('rule_id')
                    ->label('Rule')
                    ->relationship('rule', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('created_by')
                    ->label('Created by')
                    ->relationship('creator', 'email')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('points')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(10),
                Textarea::make('description')
                    ->rows(4),
                Select::make('status')
                    ->options([
                        'active' => 'active',
                        'redeemed' => 'redeemed',
                        'cancelled' => 'cancelled',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }
}
