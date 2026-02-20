<?php

namespace Modules\Penalty\Filament\Resources\PenaltyRules;

use Modules\Penalty\Models\PenaltyRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Penalty\Filament\Resources\PenaltyRules\Pages\CreatePenaltyRule;
use Modules\Penalty\Filament\Resources\PenaltyRules\Pages\EditPenaltyRule;
use Modules\Penalty\Filament\Resources\PenaltyRules\Pages\ListPenaltyRules;
use Modules\Penalty\Filament\Resources\PenaltyRules\Schemas\PenaltyRuleForm;
use Modules\Penalty\Filament\Resources\PenaltyRules\Tables\PenaltyRulesTable;

class PenaltyRuleResource extends Resource
{
    protected static ?string $model = PenaltyRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'PenaltyRule';

    public static function form(Schema $schema): Schema
    {
        return PenaltyRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenaltyRulesTable::configure($table);
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
            'index' => ListPenaltyRules::route('/'),
            'create' => CreatePenaltyRule::route('/create'),
            'edit' => EditPenaltyRule::route('/{record}/edit'),
        ];
    }
}
