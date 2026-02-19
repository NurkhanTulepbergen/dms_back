<?php

namespace Modules\Requests\Filament\Resources\RequestLives;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Requests\Filament\Resources\RequestLives\Pages\CreateRequestLive;
use Modules\Requests\Filament\Resources\RequestLives\Pages\EditRequestLive;
use Modules\Requests\Filament\Resources\RequestLives\Pages\ListRequestLives;
use Modules\Requests\Filament\Resources\RequestLives\Schemas\RequestLiveForm;
use Modules\Requests\Filament\Resources\RequestLives\Tables\RequestLivesTable;
use Modules\Requests\Models\RequestLive;

class RequestLiveResource extends Resource
{
    protected static ?string $model = RequestLive::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RequestLive';

    public static function form(Schema $schema): Schema
    {
        return RequestLiveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequestLivesTable::configure($table);
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
            'index' => ListRequestLives::route('/'),
            'create' => CreateRequestLive::route('/create'),
            'edit' => EditRequestLive::route('/{record}/edit'),
        ];
    }
}
