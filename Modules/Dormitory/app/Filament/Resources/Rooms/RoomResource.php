<?php

namespace Modules\Dormitory\Filament\Resources\Rooms;

use Modules\Dormitory\Filament\Resources\Rooms\Pages\CreateRoom;
use Modules\Dormitory\Filament\Resources\Rooms\Pages\EditRoom;
use Modules\Dormitory\Filament\Resources\Rooms\Pages\ListRooms;
use Modules\Dormitory\Filament\Resources\Rooms\Schemas\RoomForm;
use Modules\Dormitory\Filament\Resources\Rooms\Tables\RoomsTable;
use Modules\Dormitory\Models\Room;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Room';

    public static function form(Schema $schema): Schema
    {
        return RoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['floor.building', 'roomType']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRooms::route('/'),
            'create' => CreateRoom::route('/create'),
            'edit' => EditRoom::route('/{record}/edit'),
        ];
    }
}
