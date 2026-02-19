<?php

namespace Modules\Settlement\Filament\Resources\Settlements;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Settlement\Filament\Resources\Settlements\Pages\CreateSettlement;
use Modules\Settlement\Filament\Resources\Settlements\Pages\EditSettlement;
use Modules\Settlement\Filament\Resources\Settlements\Pages\ListSettlements;
use Modules\Settlement\Filament\Resources\Settlements\Schemas\SettlementForm;
use Modules\Settlement\Filament\Resources\Settlements\Tables\SettlementsTable;
use Modules\Settlement\Models\Settlement;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Settlement';

    public static function form(Schema $schema): Schema
    {
        return SettlementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettlementsTable::configure($table);
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
            'index' => ListSettlements::route('/'),
            'create' => CreateSettlement::route('/create'),
            'edit' => EditSettlement::route('/{record}/edit'),
        ];
    }
}
