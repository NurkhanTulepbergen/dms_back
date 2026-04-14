<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PenaltyRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('default_points')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(10),
                Toggle::make('redeemable')
                    ->required()
                    ->default(true),
                Toggle::make('creates_financial_charge')
                    ->required()
                    ->default(false),
                TextInput::make('financial_amount')
                    ->numeric()
                    ->minValue(0),
            ]);
    }
}
