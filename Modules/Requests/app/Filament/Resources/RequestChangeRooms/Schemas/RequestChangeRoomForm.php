<?php

namespace Modules\Requests\Filament\Resources\RequestChangeRooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\User\Models\DormStudent;

class RequestChangeRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Student')
                    ->options(function (): array {
                        return DormStudent::query()
                            ->with('user')
                            ->get()
                            ->mapWithKeys(fn (DormStudent $student) => [
                                $student->user_id => ($student->user?->email ?? ('user_id: ' . $student->user_id)),
                            ])
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'room_number')
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
                Textarea::make('description')
                    ->rows(4),
            ]);
    }
}
