<?php

namespace Modules\Requests\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('request_id')
                    ->label('Request live')
                    ->relationship('requestLive', 'id')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('path')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
