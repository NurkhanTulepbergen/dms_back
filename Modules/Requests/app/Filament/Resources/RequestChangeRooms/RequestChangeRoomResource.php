<?php

namespace Modules\Requests\Filament\Resources\RequestChangeRooms;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Requests\Filament\Resources\RequestChangeRooms\Pages\CreateRequestChangeRoom;
use Modules\Requests\Filament\Resources\RequestChangeRooms\Pages\EditRequestChangeRoom;
use Modules\Requests\Filament\Resources\RequestChangeRooms\Pages\ListRequestChangeRooms;
use Modules\Requests\Filament\Resources\RequestChangeRooms\Schemas\RequestChangeRoomForm;
use Modules\Requests\Filament\Resources\RequestChangeRooms\Tables\RequestChangeRoomsTable;
use Modules\Requests\Models\RequestChangeRoom;

class RequestChangeRoomResource extends Resource
{
    protected static ?string $model = RequestChangeRoom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RequestChangeRoom';

    public static function form(Schema $schema): Schema
    {
        return RequestChangeRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequestChangeRoomsTable::configure($table);
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
            'index' => ListRequestChangeRooms::route('/'),
            'create' => CreateRequestChangeRoom::route('/create'),
            'edit' => EditRequestChangeRoom::route('/{record}/edit'),
        ];
    }
}
