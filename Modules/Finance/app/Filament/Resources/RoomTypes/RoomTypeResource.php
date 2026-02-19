<?php

namespace Modules\Finance\Filament\Resources\RoomTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\RoomTypes\Pages\CreateRoomType;
use Modules\Finance\Filament\Resources\RoomTypes\Pages\EditRoomType;
use Modules\Finance\Filament\Resources\RoomTypes\Pages\ListRoomTypes;
use Modules\Finance\Filament\Resources\RoomTypes\Schemas\RoomTypeForm;
use Modules\Finance\Filament\Resources\RoomTypes\Tables\RoomTypesTable;
use Modules\Finance\Models\RoomType;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RoomType';

    public static function form(Schema $schema): Schema
    {
        return RoomTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomTypesTable::configure($table);
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
            'index' => ListRoomTypes::route('/'),
            'create' => CreateRoomType::route('/create'),
            'edit' => EditRoomType::route('/{record}/edit'),
        ];
    }
}
