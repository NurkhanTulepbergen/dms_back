<?php

namespace Modules\User\Filament\Resources\DormStudents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DormStudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('User ID')
                    ->sortable(),
                TextColumn::make('user.lastname')
                    ->label('Last name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('First name')
                    ->searchable(),
                TextColumn::make('user.middlename')
                    ->label('Middle name')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('user.role')
                    ->label('Role')
                    ->badge(),
                TextColumn::make('warning_count')
                    ->sortable(),
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
