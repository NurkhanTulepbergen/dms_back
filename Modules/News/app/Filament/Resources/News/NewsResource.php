<?php

namespace Modules\News\Filament\Resources\News;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\News\Filament\Resources\News\Pages\CreateNews;
use Modules\News\Filament\Resources\News\Pages\EditNews;
use Modules\News\Filament\Resources\News\Pages\ListNews;
use Modules\News\Filament\Resources\News\Schemas\NewsForm;
use Modules\News\Filament\Resources\News\Tables\NewsTable;
use Modules\News\Models\News;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'News';

    public static function form(Schema $schema): Schema
    {
        return NewsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::configure($table);
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
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }
}
