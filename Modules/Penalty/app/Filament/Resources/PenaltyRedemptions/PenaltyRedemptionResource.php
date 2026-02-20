<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRedemptions;

use Modules\Penalty\Models\PenaltyRedemption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages\CreatePenaltyRedemption;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages\EditPenaltyRedemption;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\Pages\ListPenaltyRedemptions;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\Schemas\PenaltyRedemptionForm;
use Modules\Penalty\Filament\Resources\PenaltyRedemptions\Tables\PenaltyRedemptionsTable;

class PenaltyRedemptionResource extends Resource
{
    protected static ?string $model = PenaltyRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'PenaltyRedemption';

    public static function form(Schema $schema): Schema
    {
        return PenaltyRedemptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenaltyRedemptionsTable::configure($table);
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
            'index' => ListPenaltyRedemptions::route('/'),
            'create' => CreatePenaltyRedemption::route('/create'),
            'edit' => EditPenaltyRedemption::route('/{record}/edit'),
        ];
    }
}
