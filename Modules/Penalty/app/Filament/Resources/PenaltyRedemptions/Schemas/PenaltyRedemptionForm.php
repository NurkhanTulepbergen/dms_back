<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRedemptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenaltyRedemptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('penalty_id')
                    ->label('Penalty')
                    ->relationship('penalty', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('event_type')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->rows(4),
                TextInput::make('file_path')
                    ->maxLength(255),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                    ])
                    ->required()
                    ->default('pending'),
                Select::make('reviewed_by')
                    ->label('Reviewed by')
                    ->relationship('reviewer', 'email')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
