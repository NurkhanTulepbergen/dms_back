<?php

namespace Modules\Penalty\Filament\Resources\PenaltyEvidence;

use Modules\Penalty\Models\PenaltyEvidence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\Pages\CreatePenaltyEvidence;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\Pages\EditPenaltyEvidence;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\Pages\ListPenaltyEvidence;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\Schemas\PenaltyEvidenceForm;
use Modules\Penalty\Filament\Resources\PenaltyEvidence\Tables\PenaltyEvidenceTable;

class PenaltyEvidenceResource extends Resource
{
    protected static ?string $model = PenaltyEvidence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'PenaltyEvidence';

    public static function form(Schema $schema): Schema
    {
        return PenaltyEvidenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenaltyEvidenceTable::configure($table);
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
            'index' => ListPenaltyEvidence::route('/'),
            'create' => CreatePenaltyEvidence::route('/create'),
            'edit' => EditPenaltyEvidence::route('/{record}/edit'),
        ];
    }
}
