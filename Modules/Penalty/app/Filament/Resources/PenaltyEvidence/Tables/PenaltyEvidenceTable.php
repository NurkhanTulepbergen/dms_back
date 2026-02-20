<?php

namespace Modules\Penalty\Filament\Resources\PenaltyEvidence\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenaltyEvidenceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('penalty_id')
                    ->label('Penalty')
                    ->sortable(),
                TextColumn::make('penalty.user.email')
                    ->label('Penalty User')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->file_path),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
