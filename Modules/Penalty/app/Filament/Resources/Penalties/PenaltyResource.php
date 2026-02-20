<?php

namespace Modules\Penalty\Filament\Resources\Penalties;

use Modules\Penalty\Models\Penalty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Penalty\Filament\Resources\Penalties\Pages\CreatePenalty;
use Modules\Penalty\Filament\Resources\Penalties\Pages\EditPenalty;
use Modules\Penalty\Filament\Resources\Penalties\Pages\ListPenalties;
use Modules\Penalty\Filament\Resources\Penalties\Schemas\PenaltyForm;
use Modules\Penalty\Filament\Resources\Penalties\Tables\PenaltiesTable;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Penalty';

    public static function form(Schema $schema): Schema
    {
        return PenaltyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenaltiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenalties::route('/'),
            'create' => CreatePenalty::route('/create'),
            'edit' => EditPenalty::route('/{record}/edit'),
        ];
    }
}
