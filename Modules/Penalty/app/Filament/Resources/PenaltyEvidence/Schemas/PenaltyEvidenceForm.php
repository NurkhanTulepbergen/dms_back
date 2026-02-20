<?php

namespace Modules\Penalty\Filament\Resources\PenaltyEvidence\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenaltyEvidenceForm
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
                TextInput::make('file_path')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
