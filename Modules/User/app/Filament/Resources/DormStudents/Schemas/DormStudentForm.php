<?php

namespace Modules\User\Filament\Resources\DormStudents\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DormStudentForm
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
                TextInput::make('warning_count')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),
                Placeholder::make('user_info')
                    ->label('User info')
                    ->content(function ($record): string {
                        if (! $record?->user) {
                            return 'Will be available after selecting and saving user.';
                        }

                        $user = $record->user;

                        return trim(sprintf(
                            '%s %s %s | %s | %s | uni_id: %s',
                            $user->lastname ?? '',
                            $user->name ?? '',
                            $user->middlename ?? '',
                            $user->email ?? '-',
                            $user->role ?? '-',
                            $user->uni_id ?? '-'
                        ));
                    }),
            ]);
    }
}
