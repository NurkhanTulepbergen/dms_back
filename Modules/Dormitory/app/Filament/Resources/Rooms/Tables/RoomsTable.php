<?php

namespace Modules\Dormitory\Filament\Resources\Rooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('floor.building.address')
                    ->label('Building')
                    ->searchable(),
                TextColumn::make('floor.floor_number')
                    ->label('Floor')
                    ->sortable(),
                TextColumn::make('roomType.name')
                    ->label('Room Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room_number')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->sortable(),
                TextColumn::make('live_cap')
                    ->label('Occupied')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
