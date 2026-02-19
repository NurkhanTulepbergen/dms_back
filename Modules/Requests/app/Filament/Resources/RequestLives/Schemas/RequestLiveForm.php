<?php

namespace Modules\Requests\Filament\Resources\RequestLives\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class RequestLiveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Student')
                    ->relationship('student', 'email')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('preferred_room_id')
                    ->label('Preferred room')
                    ->relationship('preferredRoom', 'room_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'accepted' => 'accepted',
                        'rejected' => 'rejected',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }
}
