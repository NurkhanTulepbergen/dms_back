<?php

namespace Modules\Finance\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('charge_id')
                    ->label('Charge')
                    ->relationship('charge', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'succeeded' => 'succeeded',
                        'failed' => 'failed',
                    ])
                    ->required()
                    ->default('pending'),
                TextInput::make('stripe_session_id')
                    ->maxLength(255),
                TextInput::make('stripe_payment_intent_id')
                    ->maxLength(255),
                DateTimePicker::make('paid_at'),
                Textarea::make('raw_payload')
                    ->rows(4),
            ]);
    }
}
