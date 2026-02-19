<?php

namespace Modules\User\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                TextInput::make('role')
                    ->required()
                    ->default('student'),
                TextInput::make('lastname'),
                TextInput::make('middlename'),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('uni_id'),
                Select::make('gender')
                    ->options([
                        'male' => 'male',
                        'female' => 'female',
                    ])
                    ->required(),
            ]);
    }
}
